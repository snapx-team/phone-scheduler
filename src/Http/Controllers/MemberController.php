<?php

namespace Xguard\PhoneScheduler\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Xguard\PhoneScheduler\Models\Member;

class MemberController extends Controller
{
    public function createMembers(Request $request, $phoneLineId)
    {
        $members = $request->all();
        try {
            foreach ($members as $employee) {
                Member::firstOrCreate([
                    'employee_id' => $employee['id'],
                    'phone_line_id' => $phoneLineId,
                ]);
            }
        } catch (\Exception $e) {
            return response([
                'success' => 'false',
                'message' => $e->getMessage(),
            ], 400);
        }
        return response(['success' => 'true'], 200);
    }

    public function deleteMember($id)
    {
        try {
            $member = Member::find($id);
            $member->delete();
        } catch (\Exception $e) {
            return response([
                'success' => 'false',
                'message' => $e->getMessage(),
            ], 400);
        }
        return response(['success' => 'true'], 200);
    }

    public function getMembers($id)
    {
        return Member::where('phone_line_id', $id)->with('employee')->get();
    }
}
