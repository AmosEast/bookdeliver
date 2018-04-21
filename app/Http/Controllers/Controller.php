<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 获得可选年级信息
     */
    protected function getGrades() {
        $grade = date('Y', time());
        $grades = [];
        for ($i = -5; $i < 3; $i++) {
            $grades[] = $grade + $i;
        }
        return $grades;
    }
}
