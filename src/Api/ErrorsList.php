<?php

namespace Api;

class ErrorsList
{
	const INCORRECT_JSON = 99;

	const EMAIL_IS_REQUIRED = 100;

	const INCORRECT_EMAIL = 101;

	const EMAIL_EXISTS = 102;

	const EMAIL_DOES_NOT_EXIST = 103;

	const NAME_IS_REQUIRED = 104;

	const NAME_IS_TOO_SHORT = 105;

	const NAME_IS_TOO_LONG = 106;

	const IS_ACTIVE_IS_REQUIRED = 107;

	const IS_ACTIVE_HAS_TO_BE_BOOLEAN = 108;
}
