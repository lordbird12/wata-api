<?php

namespace App\Http\Controllers\Zoom;

use App\Http\Controllers\Controller;
use App\Traits\ZoomJWT;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    use ZoomJWT;

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;

    function list(Request $request) {
        $path = 'users/me/meetings';
        $response = $this->zoomGet($path);

        $data = json_decode($response->body(), true);
        $data['meetings'] = array_map(function (&$m) {
            $m['start_at'] = $this->toUnixTimeStamp($m['start_time'], $m['timezone']);
            return $m;
        }, $data['meetings']);

        // return [
        //     'success' => $response->ok(),
        //     'data' => $data,
        // ];

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $data);
    }

    public function create(Request $request)
    {

        $topic = $request->topic;
        $startTime = $request->start_time;
        $agenda = $request->agenda;
        $duration = $request->duration;

        if (!isset($topic)) {
            return $this->returnErrorData('[topic] Data Not Found', 404);
        } else if (!isset($startTime)) {
            return $this->returnErrorData('[start_time] Data Not Found', 404);
        } else if (!isset($duration)) {
            return $this->returnErrorData('[duration] Data Not Found', 404);
        }

        $path = 'users/me/meetings';

        $response = $this->zoomPost($path, [
            'topic' => $topic,
            'type' => self::MEETING_TYPE_SCHEDULE,
            'start_time' => $this->toZoomTimeFormat($startTime),
            'duration' => $duration,
            'agenda' => $agenda,
            'timezone' => 'Asia/Bangkok',
            'settings' => [
                'host_video' => false,
                'participant_video' => false,
                'waiting_room' => true,
            ],
        ]);

        $data = json_decode($response->body(), true);

        $hostEmail = $data['host_email']; //mail docter
        $senderEmail = 'boss32099@gmail.com'; // mail sender

        $Topic = $data['topic'];
        $Agenda = $data['agenda'];
        $joinUrl = $data['join_url'];

        $date = date('d/m/Y', strtotime($data['start_time']));
        $time = date('H:i', strtotime($data['start_time'] . '+7 hour'));

        //send email
        $title = $topic;
        $typeMail = "Meeting Zoom With Docter";

        $description = 'ขออุนญาตินัดหมายเข้าร่วม Zoom Meeting  ในวันที่ ' . $date . ' เวลา ' . $time . ' น. ในหัวข้อการประชุม' . $Topic . ' โดยมีรายละะเอียด ' . $Agenda
            . ' สามารถเข้าร่วมการประชุมได้ที่ ' . $joinUrl;

        $this->sendMail($hostEmail, $senderEmail, $description, $title, $typeMail);

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $data);

    }
    public function get(Request $request, string $id)
    {

        $path = 'meetings/' . $id;
        $response = $this->zoomGet($path);

        $data = json_decode($response->body(), true);
        if ($response->ok()) {
            $data['start_at'] = $this->toUnixTimeStamp($data['start_time'], $data['timezone']);
        }

        // return [
        //     'success' => $response->ok(),
        //     'data' => $data,
        // ];

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $data);
    }
    public function update(Request $request, string $id)
    {
        $topic = $request->topic;
        $startTime = $request->start_time;
        $agenda = $request->agenda;
        $duration = $request->duration;

        if (!isset($topic)) {
            return $this->returnErrorData('[topic] Data Not Found', 404);
        } else if (!isset($startTime)) {
            return $this->returnErrorData('[start_time] Data Not Found', 404);
        } else if (!isset($duration)) {
            return $this->returnErrorData('[duration] Data Not Found', 404);
        }

        $path = 'meetings/' . $id;
        $response = $this->zoomPatch($path, [
            'topic' => $topic,
            'type' => self::MEETING_TYPE_SCHEDULE,
            'start_time' => (new \DateTime($startTime))->format('Y-m-d\TH:i:s'),
            'duration' => $duration,
            'agenda' => $agenda,
            'settings' => [
                'host_video' => false,
                'participant_video' => false,
                'waiting_room' => true,
            ],
        ]);

        // return [
        //     'success' => $response->status() === 204,
        //     'data' => json_decode($response->body(), true),
        // ];

        return response()->json([
            'code' => strval(201),
            'status' => true,
            'message' => 'ดำเนินการสำเร็จ',
            'data' => json_decode($response->body(), true),
        ], 201);
    }

    public function delete(Request $request, string $id)
    {
        $path = 'meetings/' . $id;
        $response = $this->zoomDelete($path);

        return $this->returnUpdate('ดำเนินการสำเร็จ');
    }
}
