@extends('layouts.basePage')

@section('css-text-part')
    <style>
        #left-navbar-main a {
            border-bottom: 1px solid #9fcdff;
        }
    </style>
@endsection

@section('content')
    <!-- 侧边导航栏 -->
    <div id="left-navbar" class="pull-left" style="width: 20%; min-height: 600px; border-right: 3px solid #ffffff;">
        <div id="left-navbar-main" class="sidebar-nav">
            <ul class="nav nav-list collpse">
                <li>
                    <a href="#order-books" class="nav-header" data-toggle="collapse" style="font-size: 1.3em;"><span class="glyphicon glyphicon-pencil"></span> &nbsp;&nbsp;&nbsp;订书模块</a>
                    <ul id="order-books" class="nav nav-list collapse" style="font-size: 1.1em;">
                        <li><a href="/"><span style="margin-left: 23%;">试试</span></a></li>
                        <li><a href="/"><span style="margin-left: 23%;">试试</span></a></li>
                    </ul>
                </li>
                <li>
                    <a href="#deliver-books" class="nav-header" data-toggle="collapse" style="font-size: 1.3em;"><span class="glyphicon glyphicon-book"></span> &nbsp;&nbsp;&nbsp;发书模块</a>
                    <ul id="deliver-books" class="nav nav-list collapse" style="font-size: 1.1em;">
                        <li><a href="/"><span style="margin-left: 23%;">试试</span></a></li>
                        <li><a href="/"><span style="margin-left: 23%;">试试</span></a></li>
                    </ul>
                </li>
                <li>
                    <a href="#query-block" class="nav-header" data-toggle="collapse" style="font-size: 1.3em;"><span class="glyphicon glyphicon-search"></span> &nbsp;&nbsp;&nbsp;查询模块</a>
                    <ul id="query-block" class="nav nav-list collapse" style="font-size: 1.1em;">
                        <li><a href="/"><span style="margin-left: 23%;">订书状态查询</span></a></li>
                        <li><a href="/"><span style="margin-left: 23%;">发书状态查询</span></a></li>
                        <li><a href="/"><span style="margin-left: 23%;">书单数量查询</span></a></li>
                    </ul>
                </li>
                <li>
                    <a href="#system-manage" class="nav-header" data-toggle="collapse" style="font-size: 1.3em;"><span class="glyphicon glyphicon-cog"></span> &nbsp;&nbsp;&nbsp;系统管理</a>
                    <ul id="system-manage" class="nav nav-list collapse" style="font-size: 1.1em;">
                        <li><a href="/"><span style="margin-left: 23%;">流程管理</span></a></li>
                        <li><a href="/"><span style="margin-left: 23%;">角色管理</span></a></li>
                        <li><a href="/"><span style="margin-left: 23%;">权限管理</span></a></li>
                        <li><a href="/"><span style="margin-left: 23%;">用户管理</span></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <!-- 右边主页面 -->
    <div id="right-main" class="pull-left" style="width:80%;">
        <iframe id="main-page-iframe" scrolling="yes" width="100%" height="100%" frameborder="0" src="{{ route('rolesmanage.index') }}" style="min-height: 600px;"></iframe>
    </div>
</div>
@endsection
