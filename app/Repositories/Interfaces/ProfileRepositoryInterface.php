<?php

namespace App\Repositories\Interfaces;

interface ProfileRepositoryInterface
{
  public function findByEmail(string $email);
  public function getUsersForNotification();
}