<?php
/**
 * Models a single email
 *
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @copyright 2014 inGenerator Ltd
 * @licence   BSD
 */

namespace Ingenerator\Mailhook;


class Email {

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var string[]
	 */
	protected $links = array();

	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * @var string
	 */
	protected $to;

	/**
	 * @param string[] $data
	 */
	public function __construct($data)
	{
		foreach ($data as $field => $value)
		{
			$this->$field = $value;
		}
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @return \string[] the urls of all links in the email
	 */
	public function getLinks()
	{
		return $this->links;
	}

	/**
	 * @param string $pattern regular expression
	 *
	 * @return \string[] the urls matching the pattern
	 */
	public function getLinksMatching($pattern)
	{
		return array_filter($this->links, function ($link) use ($pattern)
			{
				return preg_match($pattern, $link);
			}
		);
	}

	/**
	 * @return string
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @return string
	 */
	public function getTo()
	{
		return $this->to;
	}
}
