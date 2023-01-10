<?php

namespace Xguard\PhoneScheduler\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Xguard\PhoneScheduler\Models\CallLog;
use Xguard\PhoneScheduler\Models\Employee;
use Xguard\PhoneScheduler\Models\PhoneLine;
use Xguard\PhoneScheduler\Models\Row;

class PhoneScheduleController extends Controller
{
    public function getIndex()
    {

        return view('Xguard\PhoneScheduler::index');
    }

    public function getPhoneLineData($id)
    {
        $phoneLine = PhoneLine::with('members.employee', 'rows.columns.employeeCards.employee')->find($id);
        return ['phoneLine' => $phoneLine, 'openingHours' => $this->getOpeningHours($id)];
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

    public function getFormattedData(
        $id
    ) {
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

    public function getAvailableAgent(
        $id,
        $level
    ) {
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

    public function getRecentCallerInfo($id, $phone) {
        date_default_timezone_set('America/Montreal');

        $callLog = CallLog::where('phone_line_id', '=', $id)
            ->where('caller_phone', '=', $phone)
            ->whereDate('created_at', Carbon::today())->latest()->first();

        if ($callLog == null) {
            return ['phone' => ''];
        }

        $phoneLineData = PhoneLine::with(
            'members.employee',
            'rows.columns.employeeCards.employee'
        )->find($id);
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
                                return ['phone' => $employeeCard->employee->phone, 'name' => $employeeCard->employee->name];
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

    public function logCall(
        Request $request
    ) {
        $callLog = CallLog::create([
            'employee_id' => $request->input('employee_id'),
            'phone_line_id' => $request->input('phone_line_id'),
            'caller_phone' => $request->input('caller_phone'),
        ]);
    }

    public function getDirectoryData($id): array
    {
        $phoneLineData = PhoneLine::with('members.employee', 'rows.columns.employeeCards.employee')->find($id);
        $directoryData = [
            'openingHoursData' => $this->getOpeningHours($id),
            'hasAvailableAgent' => false,
            'employees' => [],
            'introMessages' =>
                [
                    'fr' => $phoneLineData['message_fr'],
                    'en' => $phoneLineData['message_en']
                ],
            'callEmployeeListText' =>
                [
                    'fr' => '',
                    'en' => ''
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
                    $filtered = $column->employeeCards->filter(function ($item) {
                        return data_get($item->employee, 'is_active') === 1;
                    });

                    if ($start > $end) {
                        $end->modify('+1 day');
                    }
                    if ($start <= $now && $now <= $end || $start <= $now->modify('+1 day') && $now <= $end || $start == $end) {
                        try {
                            if (count($filtered) > 0) {
                                $employeeIndex = 1;
                                $directoryData['hasAvailableAgent'] = true;
                                foreach ($filtered as $employeeCards) {
                                    $employee = [
                                        "phone" => $employeeCards->employee->phone,
                                        "name" => $employeeCards->employee->name,
                                        "employeeID" => $employeeCards->employee->id
                                    ];

                                    array_push($directoryData['employees'], $employee);
                                    $directoryData['callEmployeeListText']['fr'] .= ' Pour parler à '.$employeeCards->employee->name.', appuyez sur le '.$employeeIndex.'.';
                                    $directoryData['callEmployeeListText']['en'] .= ' To speak to '.$employeeCards->employee->name.', press '.$employeeIndex.'.';
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
        return [];
    }

    public function getOpeningHours($id)
    {
        $phoneLineData = PhoneLine::select('id')->with([
            'rows' => function ($q) {
                $q->select('id', 'name', 'phone_line_id');
                $q->whereHas('columns.employeeCards.employee', function ($q) {
                    $q->where('is_active', true);
                });
                $q->with([
                    'columns' => function ($q) {
                        $q->select('id', 'row_id', 'shift_start', 'shift_end');
                        $q->whereHas('employeeCards.employee', function ($q) {
                                $q->where('is_active', true);
                        });
                    }
                ]);
            }
        ])->find($id);

        list($openingHoursTexts, $english_days, $french_days) = $this->createArrayOfOpeningHours($phoneLineData['rows']);
        $openingHoursEnglish = $this->smartOpeningHoursText($english_days, $openingHoursTexts['en']);
        $openingHoursFrench = $this->smartOpeningHoursText($french_days, $openingHoursTexts['fr'], 'fr');

        return [
            'openingHoursEnText' => $openingHoursEnglish,
            'openingHoursFrText' => $openingHoursFrench,
            'openingHoursEnArray' => $openingHoursTexts['en'],
            'openingHoursFrArray' => $openingHoursTexts['fr'],
        ];
    }

    public function createArrayOfOpeningHours($rows): array
    {
        $openingHoursList = [];

        foreach ($rows as $row) {
            $openingHoursList[$row['name']] = [];

            foreach ($row['columns'] as $column) {
                array_push($openingHoursList[$row['name']], $column['shift_start']);
                array_push($openingHoursList[$row['name']], $column['shift_end']);
            }

            $lastValue = null;
            foreach ($openingHoursList[$row['name']] as $key => $value) {
                if ($value === $lastValue) {
                    unset($openingHoursList[$row['name']][$key], $openingHoursList[$row['name']][$key - 1]);
                }
                $lastValue = $value;
            }

            if (count(array_unique($openingHoursList[$row['name']])) === 1) {
                $openingHoursList[$row['name']] = [];
            }
        }

        $openingHoursTexts = ['en' => [], 'fr' => []];
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');

        foreach ($openingHoursList as $key => $openingHours) {
            $textEn = '';
            $textFr = '';
            $counter = 0;
            if (!$openingHours) {
                $textEn .= 'open 24 hours';
                $textFr .= 'ouvert 24h';
            } else {
                foreach ($openingHours as $timeFrame) {
                    if ($counter & 1) {
                        $textEn .= $timeFrame;
                        $textFr .= date("H:i", strtotime($timeFrame));
                    } else {
                        if ($counter > 0) {
                            $textEn .= ', ';
                            $textFr .= ', ';
                        }
                        $textEn .= $timeFrame.' to ';
                        $textFr .= date("H:i", strtotime($timeFrame)).' à ';
                    }
                    $counter++;
                }
            }

            $openingHoursTexts['en'][$key] = $textEn;
            $openingHoursTexts['fr'][str_replace($english_days, $french_days, $key)] = $textFr;
        }
        return array($openingHoursTexts, $english_days, $french_days);
    }

    public function smartOpeningHoursText(
        array $days,
        array &$openingHoursTexts,
        string $lang = 'en'
    ) {
        $smartText = '';
        $current = '';
        for ($y = 0; $y < count($days); $y++) {
            if (isset($openingHoursTexts[$days[$y]])) {
                if ($y < 6 &&
                    isset($openingHoursTexts[$days[$y + 1]])
                    && $openingHoursTexts[$days[$y]] === $openingHoursTexts[$days[$y + 1]]) {
                    if ($y === 0 || !isset($openingHoursTexts[$days[$y - 1]]) || isset($openingHoursTexts[$days[$y - 1]]) && $openingHoursTexts[$days[$y]] !== $openingHoursTexts[$days[$y - 1]]) {
                        if ($lang === 'en') {
                            $current .= $days[$y].' to ';
                        } else {
                            $current .= $days[$y].' à ';
                        }
                    }
                } else {
                    $current .= $days[$y].' '.$openingHoursTexts[$days[$y]];
                    $smartText .= $current.". ";
                    $current = '';

                }
            }
        }

        if ($lang === 'en') {
            return 'Our opening hours are: '.$smartText;
        } else {
            return 'Nos heures d\'ouverture sont: '.$smartText;
        }
    }
}
