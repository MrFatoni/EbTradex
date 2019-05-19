<?php

namespace App\Repositories\User\Interfaces;

interface CommentInterface
{
    public function save($attribute, $commentable);
}