<?php

namespace App\Enums;

enum Actions
{

  case LOGIN;
  case SOCIAL_LOGIN;
  case SOCIAL_AUTH_FAILED;
  case SOCIAL_AUTH_ERROR;
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
