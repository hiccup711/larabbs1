<?php

function route_class()
{
    return str_replace('.', '_', Route::currentRouteName());
}

function default_avatar()
{
    return config('app.url') . '/uploads/images/avatars/avatar.png';
}
