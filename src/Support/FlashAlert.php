<?php

namespace Nodes\Backend\Support;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class FlashAlert
 * @author  Casper Rasmussen <cr@nodes.dk>
 *
 * @package Nodes\Backend\Support
 */
class FlashAlert
{
    /**
     * @var bool|string|array
     */
    protected $error, $success, $info, $warning;

    /**
     * FlashAlert constructor.
     *
     * @param bool $error
     * @param bool $success
     * @param bool $info
     * @param bool $warning
     */
    public function __construct($error = false, $success = false, $info = false, $warning = false)
    {
        $this->error = $error ? $error : session('error');
        $this->success = $success ? $success : session('success');
        $this->info = $info ? $info : session('info');
        $this->warning = $warning ? $warning : session('info');
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param \Symfony\Component\HttpFoundation\RedirectResponse $redirectResponse
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function apply(RedirectResponse $redirectResponse)
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