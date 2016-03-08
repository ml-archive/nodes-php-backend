<?php
namespace Nodes\Backend\Support;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class FlashRestorer
 *
 * @package Nodes\Backend\Support
 */
class FlashRestorer
{
    /**
     * Error flash
     *
     * @var string|array
     */
    protected $error;

    /**
     * Success flash
     *
     * @var string|array
     */
    protected $success;

    /**
     * Info flash
     *
     * @var string|array
     */
    protected $info;

    /**
     * Warning flash
     *
     * @var string|array
     */
    protected $warning;

    /**
     * FlashRestorer constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->error = session('error');
        $this->success = session('success');
        $this->info = session('info');
        $this->warning = session('info');
    }

    /**
     * Set error flash
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  string|array $error
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * Set success flash
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  string|array $success
     * @return $this
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    /**
     * Set info flash
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  string|array $info
     * @return $this
     */
    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

    /**
     * Set warning flash
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  string|array $warning
     * @return $this
     */
    public function setWarning($warning)
    {
        $this->warning = $warning;
        return $this;
    }

    /**
     * Apply flash
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  \Symfony\Component\HttpFoundation\RedirectResponse $redirectResponse
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function apply(RedirectResponse &$redirectResponse)
    {
        // Apply error flash
        if (!empty($this->error)) {
            $redirectResponse->with('error', $this->error);
        }

        // Apply success flash
        if (!empty($this->success)) {
            $redirectResponse->with('success', $this->success);
        }

        // Apply info flash
        if (!empty($this->info)) {
            $redirectResponse->with('info', $this->info);
        }

        // Apply warning flash
        if (!empty($this->warning)) {
            $redirectResponse->with('warning', $this->warning);
        }

        return $redirectResponse;
    }
}