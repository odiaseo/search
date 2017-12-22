<?php

namespace MapleSyrupGroup\Search\Cache\Adapter;

class FilesystemJsonAdapter extends FilesystemAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function doFetch(array $ids)
    {
        $values = array();
        $now    = time();

        foreach ($ids as $id) {
            $file = $this->getFile($id);
            if (!file_exists($file)) {
                continue;
            }

            $data      = json_decode(file_get_contents($file));
            $expiresAt = $data->expires;

            if ($now >= (int) $expiresAt) {
                if (isset($expiresAt[0])) {
                    @unlink($file);
                }
            } else {
                $i     = rawurldecode(rtrim($data->id));
                $value = $data->data;
                if ($i === $id) {
                    $values[$id] = $value;
                }
            }
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave(array $values, $lifetime)
    {
        $ok        = true;
        $expiresAt = $lifetime ? time() + $lifetime : PHP_INT_MAX;
        $tmp       = $this->directory . uniqid('', true);

        foreach ($values as $id => $value) {
            $file = $this->getFile($id);

            $data = [
                'expires' => $expiresAt,
                'id'      => rawurlencode($id),
                'data'    => $value
            ];

            if (false !== @file_put_contents($tmp, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))) {
                @touch($tmp, $expiresAt);
                $ok = @rename($tmp, $file) && $ok;
            } else {
                $ok = false;
            }
        }

        return $ok;
    }
}