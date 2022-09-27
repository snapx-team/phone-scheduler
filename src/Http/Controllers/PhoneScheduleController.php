<?php

namespace Xguard\PhoneScheduler\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Xguard\PhoneScheduler\Models\CallLog;
use Xguard\PhoneScheduler\Models\Employee;
use Xguard\PhoneScheduler\Models\PhoneLine;

class PhoneScheduleController extends Controller
{
    public function getIndex()
    {

        return view('Xguard\PhoneScheduler::index');
    }

    public function getPhoneLineData($id)
    {
        $phoneLine = PhoneLine::with('members.employee', 'rows.columns.employeeCards.employee')->find($id);
        return $phoneLine;
    }

    public function getDashboardData()
    {
        $employees = Employee::orderBy('name')->get();
        $phoneLine = PhoneLine::orderBy('name')->with('members')->get();
        return [
            'employees' => $employees,
            'phoneLines' => $phoneLine
        ];
    }

    public function getDirectoryData($id)
    {
        $phoneLineData = PhoneLine::with('members.employee', 'rows.columns.employeeCards.employee')->find($id);
        $directoryData = [
            'hasAvailableAgent' => false,
            'employees' => [],
            'messages' =>
                [
                    'fr' => $phoneLineData['message_fr'],
                    'en' => $phoneLineData['message_fr']
                ]
        ];
        date_default_timezone_set('America/Montreal');

        $dayOfWeek = date("l");
        $currentTime = date('h:i a');

        foreach ($phoneLineData['rows'] as $row) {
            if ($row['name'] === $dayOfWeek) {
                foreach ($row['columns'] as $column) {
                    $now = DateTime::createFromFormat('h:i a', $currentTime);
                    $start = DateTime::createFromFormat('h:i a', $column['shift_start']);
                    $end = DateTime::createFromFormat('h:i a', $column['shift_end']);
                    if ($start > $end) {
                        $end->modify('+1 day');
                    }
                    if ($start <= $now && $now <= $end || $start <= $now->modify('+1 day') && $now <= $end || $start == $end) {
                        try {
                            $filtered = $column->employeeCards->filter(function ($item) {
                                return data_get($item->employee, 'is_active') === 1;
                            });
                            if ($filtered.count() > 0) {
                                $employeeIndex = 1;
                                $directoryData['hasAvailableAgent']= true;
                                foreach ($filtered as $employeeCards) {
                                    $employee = [
                                        "phone" => $employeeCards->employee->phone,
                                        "name" => $employeeCards->employee->name,
                                        "employeeID" => $employeeCards->employee->id
                                    ];

                                    array_push($directoryData['employees'], $employee);
                                    $directoryData['messages']['fr'] .= ' Pour parler à '.$employeeCards->employee->name.', appuyez sur le '.$employeeIndex.'.';
                                    $directoryData['messages']['en'] .= ' To speak to '.$employeeCards->employee->name.', press '.$employeeIndex.'.';
                                    $employeeIndex++;
                                }
                            }

                        } catch (\Exception $e) {
                            return $directoryData;
                        }
                        return $directoryData;
                    }
                }
            }
        }
        return ['employee_id' => '', 'name' => '', 'phone' => '',];
    }

    public function getFormattedData($id)
    {
        $phoneLineData = PhoneLine::with('members.employee', 'rows.columns.employeeCards.employee')->find($id);

        $rows = [];

        foreach ($phoneLineData['rows'] as $row) {
            $columns = [];
            foreach ($row['columns'] as $column) {
                $employeeCards = [];
                $employeeIndex = 1;
                foreach ($column['employeeCards'] as $employeeCard) {
                    if ((int) $employeeCard['employee']['is_active']) {
                        $employeeCardData = [
                            'index' => $employeeIndex, 'phone' => $employeeCard['employee']['phone'],
                            'name' => $employeeCard['employee']['name'],
                        ];
                        array_push($employeeCards, $employeeCardData);
                        $employeeIndex++;
                    }
                }
                $columnData = [
                    'timespan' => $column['name'], 'start' => $column['shift_start'], 'end' => $column['shift_end'],
                    'employee' => $employeeCards,
                ];
                array_push($columns, $columnData);
            }
            $rowData = ['day' => $row['name'], 'shifts' => $columns,];
            array_push($rows, $rowData);
        }
        return json_encode($rows);
    }

    public function getAvailableAgent($id, $level)
    {
        $level--; // we start at index 0
        $phoneLineData = PhoneLine::with('members.employee', 'rows.columns.employeeCards.employee')->find($id);

        date_default_timezone_set('America/Montreal');

        $dayOfWeek = date("l");
        $currentTime = date('h:i a');

        foreach ($phoneLineData['rows'] as $row) {
            if ($row['name'] === $dayOfWeek) {
                foreach ($row['columns'] as $column) {
                    $now = DateTime::createFromFormat('h:i a', $currentTime);
                    $start = DateTime::createFromFormat('h:i a', $column['shift_start']);
                    $end = DateTime::createFromFormat('h:i a', $column['shift_end']);

                    if ($start > $end) {
                        $end->modify('+1 day');
                    }
                    if ($start <= $now && $now <= $end || $start <= $now->modify('+1 day') && $now <= $end || $start == $end) {
                        try {
                            $filtered = $column->employeeCards->filter(function ($item) {
                                return data_get($item->employee, 'is_active') === 1;
                            });

                            $phone = $filtered->values()->get($level)->employee->phone;
                            $name = $filtered->values()->get($level)->employee->name;
                            $employeeID = $filtered->values()->get($level)->employee->id;
                        } catch (\Exception $e) {
                            return ['employee_id' => '', 'name' => '', 'phone' => '',];
                        }
                        return ['employee_id' => $employeeID, 'name' => $name, 'phone' => $phone,];
                    }
                }
            }
        }
        return ['employee_id' => '', 'name' => '', 'phone' => '',];
    }

    public function getRecentCallerInfo(Request $request)
    {
        date_default_timezone_set('America/Montreal');

        $callLog = CallLog::where('phone_line_id', '=', $request->input('phone_line_id'))
            ->where('caller_phone', '=', $request->input('caller_phone'))
            ->whereDate('created_at', Carbon::today())->latest()->first();

        if ($callLog == null) {
            return ['phone' => ''];
        }

        $phoneLineData = PhoneLine::with(
            'members.employee',
            'rows.columns.employeeCards.employee'
        )->find($request->input('phone_line_id'));
        $dayOfWeek = date("l");
        $currentTime = date('h:i a');

        foreach ($phoneLineData['rows'] as $row) {
            if ($row['name'] === $dayOfWeek) {
                foreach ($row['columns'] as $column) {
                    $now = DateTime::createFromFormat('h:i a', $currentTime);
                    $start = DateTime::createFromFormat('h:i a', $column['shift_start']);
                    $end = DateTime::createFromFormat('h:i a', $column['shift_end']);

                    if ($start > $end) {
                        $end->modify('+1 day');
                    }
                    if ($start <= $now && $now <= $end || $start <= $now->modify('+1 day') && $now <= $end || $start == $end) {
                        try {
                            $filtered = $column->employeeCards->filter(function ($item) {
                                return data_get($item->employee, 'is_active') === 1;
                            });
                        } catch (\Exception $e) {
                            return ['phone' => ''];
                        }

                        foreach ($filtered as $employeeCard) {
                            if ($employeeCard->employee->id == $callLog->employee_id) {
                                return ['phone' => $employeeCard->employee->phone];
                            } else {
                                return ['phone' => ''];
                            }
                        }
                    }
                }
            }
        }
        return ['phone' => ''];
    }

    public function logCall(Request $request)
    {
        $callLog = CallLog::create([
            'employee_id' => $request->input('employee_id'),
            'phone_line_id' => $request->input('phone_line_id'),
            'caller_phone' => $request->input('caller_phone'),
        ]);
    }
}
