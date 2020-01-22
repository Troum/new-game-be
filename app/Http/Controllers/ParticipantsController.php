<?php

namespace App\Http\Controllers;

use App\Exports\ParticipantsExport;
use App\Participant;
use App\Traits\System;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class ParticipantsController extends Controller
{
    use System;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function participate(Request $request)
    {
        if($this->check($request->check_number)) {
            $participant = new Participant();
            $participant->store($request);
            Storage::putFileAs('participants',
                $request->file('file'),
                $participant->id . '.' . $request->file('file')->getClientOriginalExtension());
            $participant->image = $participant->id . '.' . $request->file('file')->getClientOriginalExtension();
            $participant->save();
            return response()->json(['success' => 'Вы успешно зарегистрировались! Ваша заявка будет обработана в течении 24 часов'], Response::HTTP_OK);
        } else {
            return response()->json(['error' => 'Увы, но участник с таким номером чека уже зарегистрирован'], Response::HTTP_CONFLICT);
        }

    }

    /**
     * @return JsonResponse
     */
    public function participants()
    {
        $registered = $this->convert(Participant::whereAccepted(0)->with('chances')->get());
        $approved = $this->convert(Participant::whereAccepted(1)->with('chances')->get());
        $declined = $this->convert(Participant::whereAccepted(2)->with('chances')->get());
        return response()->json(['registered' => $registered, 'approved' => $approved, 'declined' => $declined], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function export(Request $request)
    {
         $from = Carbon::createFromTimeString($request->from)->subHours(3)->format('Y-m-d H:i:s');
         $to = Carbon::createFromTimeString($request->to)->subHours(3)->format('Y-m-d H:i:s');
         Excel::store(new ParticipantsExport($from, $to), 'export.xlsx');
         return response()->json(['export' => true], Response::HTTP_OK);
    }

    /**
     * @return mixed
     */
    public function getExported()
    {
        return Storage::download('export.xlsx');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getImage(Request $request)
    {
        $path = Storage::get('participants/' . $request->url);
        return Image::make($path)->response();

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function approve(Request $request)
    {
        $participant = Participant::whereId($request->id)->first();
        for ($i = 1; $i <= $request->chance; $i++) {
            $chance = $participant->chances()->create([
               'participant_id' => $participant->id
            ]);
            $chance->update([
                'chance' => $this->generate($chance->id)
            ]);
        }
        $participant->update([
            'accepted' => 1
        ]);

        if($this->sendApprove($participant)) {
            return $this->participants();
        } else {
            return response()->json(['error' => 'Пользователь промодерирован (одобрен), однако письмо не было отправлено'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function decline(Request $request)
    {
        $participant = Participant::whereId($request->id)->first();

        $participant->update([
            'accepted' => 2
        ]);

        if($this->sendDecline($participant, $request->reason)) {
            return $this->participants();
        } else {
            return response()->json(['error' => 'Пользователь промодерирован (отклонен), однако письмо не было отправлено'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
