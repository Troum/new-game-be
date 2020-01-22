<?php

namespace App\Http\Controllers;

use App\Result;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class ResultController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function result(Request $request)
    {
        if (isset(json_decode($request->get('array')[0])->prize) && isset(json_decode($request->get('array')[0])->chance)) {
            $result = Result::create([
                'title' => $request->resultDate
            ]);

            foreach ($request->get('array') as $item) {
                $result->winners()->create([
                    'name' => json_decode($item)->name,
                    'check_number' => json_decode($item)->check_number,
                    'chance' => json_decode($item)->chance,
                    'prize' => json_decode($item)->prize
                ]);
            }

            return response()->json(['success' => 'Розыгрыш успешно добавлен'], Response::HTTP_OK);
        }

        return response()->json(['error' => 'Проверьте, добавлен ли приз и номер игрового шанса'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @return JsonResponse
     */
    public function results()
    {
        $results = Result::with('winners')->get();
        return response()->json(['results' => $results], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteResult(Request $request)
    {
        $result = Result::whereId($request->id)->firstOrFail();
        $result->delete();
        return $this->results();
    }
}
