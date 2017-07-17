<?php 
namespace Conpoz\App\Lib\ValidateRule;

class Register extends \Conpoz\Core\Lib\Util\Validator\ValidateRule
{
    public $account = array(
        'required' => 'account is required',
        'email' => 'account need valid email format'
        );
    public $password = array(
        'required' => 'password required',
        'min-length:6' => 'password\'s min lenght is 6',
        'max-length:60' => 'password\'s max lenght is 60',
        );

    public $retype_password = array(
        'required' => 'retype password plz',
        'compare-with:password' => 'password different with retype password'
        );
    public $name = array(
        'required' => 'name is required',
        'min-length:2' => 'name\'s min lenght is 2',
        'max-length:20' => 'name\'s max lenght is 20',
    );
    public $channel = array(
        'required' => 'channel is required',
        'min-length:2' => 'channel\'s min lenght is 2',
        'max-length:20' => 'channel\'s max lenght is 20',
    );
}