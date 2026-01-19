<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;

class CustomLdapUser extends LdapUser implements AuthorizableContract
{
    use Authorizable;
}
