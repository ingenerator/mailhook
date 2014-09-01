<?php
/**
 * Defines Ingenerator\Mailhook\EmailParser
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace Ingenerator\Mailhook;

/**
 * Parses a single email into an Email object
 *
 * @package Ingenerator\Mailhook
 * @see     spec\Ingenerator\Mailhook\EmailParserSpec
 */
class EmailParser {

	/**
	 * @param string $raw_message
	 *
	 * @return Email
	 */
	public function parse($raw_message)
	{
		list($headers, $content) = explode("\n\n", $raw_message, 2);

		$data = array(
			'to'      => NULL,
			'subject' => NULL,
			'content' => quoted_printable_decode($content)
		);

		$data['links'] = $this->parseLinksFromContent($data['content']);

		if (preg_match('/^To:\s+<?(.+?)>?$/m', $headers, $matches))
		{
			$data['to'] = $matches[1];
		}

		if (preg_match('/^Subject:\s+(.+?)$/m', $headers, $matches))
		{
			$data['subject'] = $matches[1];
		}

		return new Email($data);
	}

	/**
	 * @param string $content
	 *
	 * @return string[]
	 */
	protected function parseLinksFromContent($content)
	{
		if (preg_match_all('_https?://[^\s^"^<]+_', $content, $matches))
		{
			$links = $matches[0];
			return array_values(array_unique($links));
		}
		else
		{
			return array();
		}
	}
}
