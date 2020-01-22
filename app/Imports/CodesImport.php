<?php

namespace App\Imports;

use App\Code;
use Maatwebsite\Excel\Concerns\ToModel;

class CodesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (!isset($row[0])) {
            return null;
        }
        return new Code([
            'code' => $row[0]
        ]);
    }
}
