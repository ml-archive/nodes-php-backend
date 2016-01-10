<?php
namespace Nodes\Backend\Dashboard\Types;

use Illuminate\Support\Str;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;

class IFrame
{
    /**
     * @var string
     */
    protected $type = 'i-frame';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $id;

    /**
     * IFrame constructor.
     *
     * @param array $config
     * @throws \Nodes\Backend\Dashboard\Exceptions\MissingConfigException
     */
    public function __construct(array $config)
    {
        // Guard title param
        if (empty($config['title']) || !is_string($config['title'])) {
            throw new MissingConfigException('Missing title');
        }

        // Guard title url param
        if (empty($config['url']) || !is_string($config['url'])) {
            throw new MissingConfigException('Missing url');
        }

        // Set params
        $this->title = $config['title'];
        $this->url = $config['url'];

        // Assign random id
        $this->id = Str::random();
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}