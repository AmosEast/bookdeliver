<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PracticeController extends Controller
{
    /**
     * 用于测试controller和学习的控制器
     */
    public function index() {
        $data = [
            'data' => [
                'first' =>'<strong>I want to show you</strong> ',
                'second' =>'<strong>what i am inside</strong>'
            ],
            'msg' => '<strong>there is nothing but me</strong>'
        ];
        $first = '<strong>I want to show you</strong> ';
        $second = '<strong>what i am inside</strong>';
        /** 传参方法一 */
        return view("practice.index") ->with($data);
        /** 传参方法二 */
//        return view("practiceIndex", $data);
        /** 传参方法三 */
//        return view("practiceIndex") ->with('first', $first);
    }
}
