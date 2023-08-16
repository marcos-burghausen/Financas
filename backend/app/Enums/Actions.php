<?php

namespace App\Enums;

enum Actions
{

  case LOGIN;
  case USER_OR_PASSWORD_INVALID;
  case ME;
  case LOGOUT;






  case SIGN_UP_ASSOCIATE_DATA;
  case SIGN_UP_STORED;

  case SEND_TOKEN;
  case RESEND_TOKEN;
  case VALIDATE_TOKEN;

  case IMAGE_CREATE;
  case IMAGE_UPDATE;

  case ASSOCIATE_DATA;


  case UPDATE_PASSWORD;
  case RECOVER_PASSWORD;


  public function getAction()
  {
    return $this->name;
  }
}
