<?php

namespace Shopsys\FrameworkBundle\Command;

use Redis;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisCleanCacheCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:redis:clean-cache';

    /**
     * @var \Redis[]
     */
    private $redisClients;

    /**
     * @param \Redis[] $redisClients
     */
    public function __construct(array $redisClients)
    {
        $this->redisClients = $redisClients;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Cleans up redis cache');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->redisClients as $redis) {
            $prefix = $redis->getOption(Redis::OPT_PREFIX);
            $keys = $redis->keys('*');
            $plainKeys = $this->removePrefixes($keys, $prefix);
            $redis->del($plainKeys);
        }
    }

    /**
     * @param string[] $keys
     * @param string $prefix
     * @return string[]
     */
    private function removePrefixes(array $keys, string $prefix): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[] = preg_replace(sprintf('~^%s~', $prefix), '', $key);
        }
        return $result;
    }
}
