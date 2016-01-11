<?php

namespace Nodes\Backend\Support;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class FlashRestorer
 * @author  Casper Rasmussen <cr@nodes.dk>
 *
 * @package Nodes\Backend\Support
 */
class FlashRestorer
{
    /**
     * Variable for error
     * @var string|array
     */
    protected $error;

    /**
     * Variable for success
     * @var string|array
     */
    protected $success;

    /**
     * Variable for info
     * @var string|array
     */
    protected $info;

    /**
     * Variable for warning
     * @var string|array
     */
    protected $warning;

    /**
     * FlashRestorer constructor.
     */
    public function __construct()
    {
        $this->error = session('error');
        $this->success = session('success');
        $this->info = session('info');
        $this->warning = session('info');
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param string|array $error
     * @return $this
     */
    public function setError($error) {
        $this->error = $error;

        return $this;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param string|array $success
     * @return $this
     */
    public function setSuccess($success) {
        $this->success = $success;

        return $this;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param string|array $info
     * @return $this
     */
    public function setInfo($info) {
        $this->info = $info;

        return $this;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param string|array $warning
     * @return $this
     */
    public function setWarning($warning) {
        $this->warning = $warning;

        return $this;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param \Symfony\Component\HttpFoundation\RedirectResponse $redirectResponse
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function apply(RedirectResponse &$redirectResponse)
    {
        // Apply error
        if($this->error) {
            $redirectResponse->with('error', $this->error);
        }

        // Apply success
        if($this->success) {
            $redirectResponse->with('success', $this->success);
        }

        // Apply info
        if($this->info) {
            $redirectResponse->with('info', $this->info);
        }

        // Apply warning
        if($this->warning) {
            $redirectResponse->with('warning', $this->warning);
        }

        return $redirectResponse;
    }
}