<?php
/**
 * Defines Ingenerator\Mailhook\Mailhook
 *
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @copyright 2014 inGenerator Ltd
 * @licence   BSD
 */

namespace Ingenerator\Mailhook;

use Ingenerator\Mailhook\Assert\NegativeAssertionRunner;
use Ingenerator\Mailhook\Assert\PositiveAssertionRunner;

/**
 * Manages the postfix mail dump, retrieving and parsing new emails
 *
 * @package Ingenerator\Mailhook
 * @see     spec\Ingenerator\Mailhook\MailhookSpec
 */
class Mailhook {

	/**
	 * @var MailhookAsserter
	 */
	protected $asserter;

	/**
	 * @var string
	 */
	protected $dump_file;

	/**
	 * @var int
	 */
	protected $dump_tail_psn;

	/**
	 * @var Email[]
	 */
	protected $emails = array();

	/**
	 * @var EmailParser
	 */
	protected $parser;

	/**
	 * @param string           $dump_file
	 * @param null|EmailParser $parser
	 */
	public function __construct($dump_file, EmailParser $parser = NULL)
	{
		$this->dump_file = $dump_file;
		$this->parser    = $parser ? $parser : new EmailParser;
	}

	public function assert()
	{
		if ( ! $this->asserter)
		{
			$filterer       = new EmailListFilterer;
			$this->asserter = new MailhookAsserter(
				new PositiveAssertionRunner($this, $filterer),
				new NegativeAssertionRunner($this, $filterer)
			);
		}

		return $this->asserter;
	}

	/**
	 * @return Email[]
	 */
	public function getEmails()
	{
		return $this->emails;
	}

	/**
	 * @return bool
	 */
	public function hasEmails()
	{
		return (bool) count($this->emails);
	}

	/**
	 * Remove the current dump file and purge all stored emails
	 *
	 * @throws \RuntimeException
	 */
	public function purge()
	{
		if (file_exists($this->dump_file))
		{
			$deleted = (is_writable($this->dump_file) AND unlink($this->dump_file));
			if ( ! $deleted)
			{
				throw new \RuntimeException('Could not delete mail dump at ' . $this->dump_file);
			}
		}
		$this->emails        = array();
		$this->dump_tail_psn = NULL;
	}

	/**
	 * Load new mails from the dump
	 *
	 * @return void
	 */
	public function refresh()
	{
		$mails = $this->loadNewMailsFromDump();
		foreach ($mails as $mail)
		{
			$this->emails[] = $this->parser->parse($mail);
		}
	}

	/**
	 * @return string[]
	 */
	protected function loadNewMailsFromDump()
	{
		$mails = array();

		if ( ! file_exists($this->dump_file))
		{
			return $mails;
		}

		$dump = $this->openDumpFileTail();

		$mail_index = 0;
		while ($line = fgets($dump))
		{
			// Identify the start of a new message
			if ($this->isMailDumpSeparatorLine($line))
			{
				$mail_index++;
				$mails[$mail_index] = '';
			}
			$mails[$mail_index] .= $line;
		}

		$this->closeDumpFileTail($dump);

		return $mails;
	}

	/**
	 * @return resource
	 */
	protected function openDumpFileTail()
	{
		$dump = fopen($this->dump_file, 'r');
		if ($this->dump_tail_psn)
		{
			fseek($dump, $this->dump_tail_psn);
		}

		return $dump;
	}

	/**
	 * @param string $line
	 *
	 * @return int
	 */
	protected function isMailDumpSeparatorLine($line)
	{
		return preg_match('/^From [^\s]+\s*[\w ]+?[0-9]{2}:[0-9]{2}:[0-9]{2} [0-9]{4}$/m', $line);
	}

	/**
	 * @param resource $dump
	 */
	protected function closeDumpFileTail($dump)
	{
		$this->dump_tail_psn = ftell($dump);
		fclose($dump);
	}
}
