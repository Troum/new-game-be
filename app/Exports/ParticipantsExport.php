<?php

namespace App\Exports;

use App\Participant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ParticipantsExport implements FromQuery, WithMapping, WithColumnFormatting, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return Participant::query()
            ->where('created_at', '>=', $this->from)
            ->where('created_at', '<=', $this->to)
            ->where('accepted', '=', 1)
            ->with('chances');
    }

    /**
     * @param mixed $participant
     * @return array
     */
    public function map($participant): array
    {
        return [
            $participant->surname,
            $participant->name,
            $participant->secondName,
            $participant->check_number,
            $participant->date,
            $participant->phone,
            $participant->email,
            $participant->address,
            $this->whereFrom($participant),
            $this->chances($participant->chances),
            Carbon::createFromFormat('Y-m-d H:i:s', $participant->created_at)->format('d.m.Y')
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'D' => '+###(##)###-##-##'
        ];
    }

    public function headings(): array
    {
        return [
            'Фамилия',
            'Имя',
            'Отчество',
            'Номер чека',
            'Дата покупки',
            'Номер телефона',
            'Адрес электронной почты',
            'Адрес проживания',
            'Игровые шансы',
            'Откуда',
            'Дата регистрации',
        ];
    }

    public function chances($chances)
    {
        $string = '';
        foreach ($chances as $chance){
            $string = $string . $chance->chance . ",\n";
        }

        return rtrim(trim(str_replace("\"", '', $string)), ',');
    }

    /**
     * @param Model $participant
     * @return string
     */
    public function whereFrom(Model $participant) {
        if ($participant->fromVk == 1) {
            return 'Через ВК';
        }
        elseif ($participant->fromTelegram == 1) {
            return 'Через Telegram';
        }
        
        return 'Через сайт';
    }

}
