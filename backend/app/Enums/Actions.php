<?php

namespace App\Enums;

enum Actions
{

  case LOGIN;
  case USER_OR_PASSWORD_INVALID;
  case ME;
  case REFRESH_TOKEN;
  case USER_CREATE_NEW_WALLET;
  case LOGOUT;
  public function getAction()
  {
    return $this->name;
  }
}
