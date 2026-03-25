<?php
declare(strict_types=1);

class TeamSpeakCache
{
    private string $file;
    private int $ttl;

    public function __construct(string $key, int $ttl)
    {
        $this->ttl = max(5, $ttl);
        $this->file = sys_get_temp_dir() . '/nexpell_ts_' . md5($key) . '.cache';
    }

    public function get(): ?array
    {
        if (!is_file($this->file)) {
            return null;
        }

        if (filemtime($this->file) + $this->ttl < time()) {
            return null;
        }

        $data = @file_get_contents($this->file);
        return $data ? unserialize($data) : null;
    }

    public function set(array $data): void
    {
        @file_put_contents($this->file, serialize($data), LOCK_EX);
    }
}
