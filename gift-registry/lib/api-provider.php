<?php
/**
 * API provider framework.
 *
 * @package plai2010
 * @subpackage api
 * @copyright Copyright (c) 2014,2015 Patrick Lai
 */

namespace PLai2010\ApiFramework;

use Exception;

/**
 * API provider helper.
 */
class ApiProvider /*{{{*/
{
	/** Pseudo parameter name: entity body. */
	const PARAM_BODY = '*body';

	/** Pseudo parameter name: options. */
	const PARAM_OPTIONS = '*opts';

	/** Parameter source: request body. */
	const PARAM_SRC_BODY = '*body';

	/** Parameter source: name-value map. */
	const PARAM_SRC_VALUES = '*values';

	/** Parameter source: name-reference map. */
	const PARAM_SRC_REFS = '*refs';

	/** Parameter source: name-option map. */
	const PARAM_SRC_OPTS = '*opts';

	/** Error label: unknown API. */
	const ERR_API_UNKNOWN = 'api-unknown';

	/** Error label: API exception. */
	const ERR_API_EXCEPTION = 'api-exception';

	/** Error label: unsupported feature. */
	const ERR_UNSUPPORTED = 'api-unsupported';

	/** Error label: invalid input. */
	const ERR_BAD_INPUT = 'api-bad-input';

	/** Error label: unauthorized API invocation. */
	const ERR_NO_AUTHZ = 'api-no-authz';

	/** @var string Message tag in exception by {@link makeApiException()}. */
	protected static $API_EXCEPTION_MSG_TAG = '@@@API-EXCEPTION@@@';

	/**
	 * List of API definitions.
	 *
	 * API detail includes the following:
	 * <ul>
	 * <li>pattern: Regular expression to match request "method:path".
	 *     For example, '%^GET:/alumni/([0-9]+)$%' would match a GET
	 *     /alumni/123.
	 * <li>params: additional parameter sources (e.g. {@link PARAM_SRC_BODY},
	 *     a map of parameters, etc.)
	 * <li>impl: implementation callback
	 * <li>args: argument descriptors for impl method; it is a list, each
	 *     item being an integer (for matched sub-pattern) or a string
	 *     name (for request parameter)
	 * <li>marshal: optional callback to massage result; it is given
	 *     two arguments: the return value from the impl method,
	 *     and the argument list to the impl method
	 * <li>authz: optional authorization specification
	 * </ul>
	 *
	 * @var array List of API definitions.
	 */
	protected $api_defs;

	/**
	 * Owner of this provider, with this object as default.
	 * This is used by {@link invokeCallback()}, for example.
	 * @var object|string
	 */
	protected $owner;

	/**
	 * Callback to check if a user is authorized to invoke an API.
	 * It is invoked by {@link executeApi()} and takes three arguments:
	 * authz specification, the calling user, and arguments prepared for
	 * implementation method.
	 * @var callback Return true if user is authorized.
	 */
	protected $authz_check;

	/**
	 * Construct an API provider.
	 * @param array $apiDefinitionsAPI definitions.
	 * @param mixed $owner Owner of this provider.
	 * @param callback $permCheck Callback to check API invocation permission.
	 */
	public function __construct($apiDefinitions, $owner=null, $permCheck=null) {
		$this->api_defs = $apiDefinitions;
		$this->owner = $owner;
		$this->authz_check = $permCheck;
	}

	/**
	 * Invoke a callback.
	 * We allow a special form of (null, method), where we inject
	 * this the owner to replace the null.
	 * @param callback $func Callback to invoke.
	 * @param array Invocation arguments.
	 * @return mixed Result from callback.
	 */
	protected function invokeCallback($func, $args) {
		if (is_array($func) && !isset($func[0]))
			$func[0] = $this->owner;
		return call_user_func_array($func, $args);
	}

	/**
	 * Create API error response.
	 * @param string $label Error lable.
	 * @param string $msg Error message.
	 * @param mixed $details Error details.
	 * @return array Error response object.
	 */
	public function makeApiError($label, $msg=null, $details=null) {
		$error = array(
			'label' => $label,
		);
		if (isset($msg))
			$error['msg'] = $msg;
		if (isset($details))
			$error['details'] = $details;

		return array(
			'_err' => $error,
		);
	}

	/**
	 * Create exception to represent an API error.
	 * The exception message is the concatenation of
	 * {@link $API_EXCEPTION_MSG_TAG} and the API error response in JSON.
	 * @param string $label Error lable.
	 * @param string $msg Error message.
	 * @param mixed $details Error details.
	 * @return Exception
	 */
	public function makeApiException($label, $msg=null, $details=null) {
		$result = $this->makeApiError($label, $msg, $details);
		return new Exception(self::$API_EXCEPTION_MSG_TAG.json_encode($result));
	}

	/**
	 * Decode an API exception into an error response.
	 * The exception should have been created by {@link makeApiException()}.
	 * @param Exception $ex Exception to decode.
	 * @returns array API error response.
	 */
	public function decodeApiException($ex) {
		$msg = $ex->getMessage();

		$tagLen = strlen(self::$API_EXCEPTION_MSG_TAG);
		if (substr($msg, 0, $tagLen) == self::$API_EXCEPTION_MSG_TAG) {
			$response = json_decode(substr($msg, $tagLen), true);
			if (is_array($response))
				return $response;
		}
		else {
			static $SEED;
			if (empty($SEED))
				$SEED = openssl_random_pseudo_bytes(16);

			// for non-API exception, we log the exception message with
			// some tag, and return the tag instead of the message to
			// the client (to avoid exposure of sensitive info)
			//
			$tag = base64_encode(md5($SEED.microtime(), true));
			$tag = str_replace(array('+', '/'), array('-', '_'), $tag);
			$tag = substr($tag, 0, 12);

			error_log("API-ERROR-TAG:$tag $msg");
			$msg = "error-tag:$tag";
		}


		return $this->makeApiError(self::ERR_API_EXCEPTION, $msg);
	}

	/**
	 * Check if an API result indicates error.
	 * The exception should have been created by {@link makeApiException()}.
	 * @param array $result API result.
	 * @param string &$msg Error message.
	 * @param mixed &$details Error details.
	 * @returns string Error label or null.
	 */
	public function checkApiError($result, &$msg=null, &$details=null) {
		$msg = $details = null;

		if (empty($result['_err']['label']))
			return null;
		$error = $result['_err'];

		$msg = isset($error['msg']) ? $error['msg'] : null;
		$details = isset($error['details']) ? $error['details'] : null;
		return $error['label'];
	}

	/**
	 * Match request for an alumni DB RESTful API.
	 * @param string $method HTTP request method.
	 * @param string $path Request path (e.g. '/alumni/123')
	 * @param string $flag Special flag to match.
	 * @return mixed Execution context of matched API; null if nothing matched.
	 */
	public function matchApi($method, $path, $flag=null) {
		// make sure path begins with '/'
		//
		if (empty($path) || substr($path, 0, 1) != '/')
			$path = "/$path";

		// matching includes method and path
		//
		$subject = "$method:$path";

		// match APIs one by one
		// TODO: faster way?
		//
		$ctx = null;
		foreach ($this->api_defs as $def) {
			$args = array();
			if (!preg_match($def['pattern'], $subject, $args))
				continue;

			if (isset($flag)) {
				// match flag required
				//
				if (empty($def['flags']) || !in_array($flag, $def['flags']))
					break;
			}

			// arguments are matched sub-patterns; shift out the whole
			//
			array_shift($args);

			// prepare invocation context based on matched API
			//
			$ctx['def'] = $def;
			$ctx['args'] = $args;

			$ctx['method'] = $method;
			$ctx['path'] = $path;
			$ctx['flag'] = $flag;
			break;
		}

		return $ctx;
	}

	/**
	 * Unmarshal content, typically a request body.
	 * @param string $ctype Content type.
	 * @param string $data Content in marshalled form.
	 * @return mixed Null if no request body.
	 */
	protected function unmarshalContent($ctype, $data) {
		if (empty($ctype)) {
			if (empty($data))
				return null;
			$ctype = 'x-www-form-urlencoded';
		}
		else {
			$match = array();
			$pattern = '%application/(x-www-form-urlencoded|json)%';
			if (!preg_match($pattern, $ctype, $match))
				throw $this->makeApiException(
					self::ERR_UNSUPPORTED, "unsupported content-type: $ctype");
			$ctype = $match[1];
		}

		switch ($ctype) {
		case 'json':
			if (($result = json_decode($data, true)) === null)
				throw $this->makeApiException(
						self::ERR_BAD_INPUT, 'malformed JSON');
			break;
		case 'x-www-form-urlencoded':
			$result = array();
			parse_str($data, $result);
			break;
		default:
			throw $this->makeApiException(
					self::ERR_UNSUPPORTED, "unknown content-type: $ctype");
		}

		return $result;
	}

	/**
	 * Execute alumni DB RESTful API.
	 * @param mixed $ctx Execution context from {@link matchApi()}.
	 * @param mixed $user Authenticated user.
	 * @param array $inputs Request parameters.
	 * @param callback $body Callback to get entity body as (type, data) tuple.
	 * @return array API response.
	 */
	public function executeApi($ctx, $user, $inputs, $body) {
		$def = $ctx['def'];
		$flag = $ctx['flag'];

		// additional parameters
		//
		if (!empty($def['params'])) {
			foreach ($def['params'] as $type => $src) {
				switch ($type) {
				case self::PARAM_SRC_BODY:
					// merge parameters from request body
					//
					list($ctype, $data) = call_user_func($body);
					$params = $this->unmarshalContent($ctype, $data);
					if (!isset($params))
						break;
					if (!is_array($params))
						throw $this->makeApiException(self::ERR_BAD_INPUT,
							'body is not name-value pairs');

					assert('is_array($src)');
					foreach ($src as $from => $to) {
						$v = isset($params[$from]) ? $params[$from] : null;
						$inputs[$to] = $v;
					}
					break;
				case self::PARAM_SRC_VALUES:
					assert('is_array($src)');
					foreach ($src as $n => $v)
						$inputs[$n] = $v;
					break;
				case self::PARAM_SRC_REFS:
					assert('is_array($src)');
					foreach ($src as $n => $v) {
						if (is_integer($v)) {
							// reference to URL matched arguments
							//
							assert('array_key_exists($v, $ctx["args"])');
							$inputs[$n] = $ctx['args'][$v];
							continue;
						}
						$inputs[$n] = $inputs[$v];
					}
					break;
				case self::PARAM_SRC_OPTS:
					if (is_callable($src)) {
						$inputs[self::PARAM_OPTIONS] =
								call_user_func($src, $inputs, $flag);
						break;
					}

					assert('is_array($src)');
					$opts = null;
					foreach ($src as $from => $to) {
						if (!isset($inputs[$from]))
							continue;
						$opts[$to] = $inputs[$from];
					}
					if (!empty($opts))
						$inputs[self::PARAM_OPTIONS] = $opts;
					break;
				default:
					throw new Exception("unknown parameter source type: $type");
				}
			}
		}

		// prepare arguments for implementation method
		//
		$args = array();
		if (!empty($def['args'])) {
			foreach ($def['args'] as $offset) {
				if (is_int($offset)) {
					// argument is matched sub-pattern from URL
					//
					assert('array_key_exists($offset, $ctx["args"])');
					$args[] = $ctx['args'][$offset];
				}
				else if ($offset == self::PARAM_BODY) {
					// argument is request body
					//
					list($ctype, $data) = call_user_func($body);
					$args[] = $this->unmarshalContent($ctype, $data);
				}
				else {
					// argument is request parameter by name or some
					// prepared inputs
					//
					$args[] = isset($inputs[$offset]) ? $inputs[$offset] : null;
				}
			}
		}

		// authz check
		//
		if (isset($this->authz_check) && isset($def['authz'])) {
			$authz = $this->invokeCallback($this->authz_check, array(
				$def['authz'],
				$user,
				$args,
			));
			if (!$authz)
				throw $this->makeApiException(self::ERR_NO_AUTHZ);
		}

		// invoke implementation method
		//
		$result = $this->invokeCallback($def['impl'], $args);

		// turn result to response
		//
		if (isset($def['marshal']))
			$result = $this->invokeCallback($def['marshal'], array(
				$result,
				$args,
			));

		return $result;
	}
} /*}}}*/

// vim: set ts=4 noexpandtab fileformat=unix fdm=marker syntax=php:
