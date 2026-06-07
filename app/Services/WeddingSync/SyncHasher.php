<?php

namespace App\Services\WeddingSync;

class SyncHasher
{
    private array $ignoredKeys = [
        '_sheet_row',
        '_sheet_name',
        'sheet_row',
        'sheet_name',
        'source_web_key',
        'sync_action',
        'sync_source',
        'sync_note',
        'web_id',
        'web_model',
        'last_synced_at',
        'last_checked_at',
    ];

    private function comparablePayload(array $payload): array
    {
        foreach ($this->ignoredKeys as $key) {
            unset($payload[$key]);
        }

        ksort($payload);

        return $payload;
    }

    public function make(array $payload): string
    {
        $cleanPayload = $this->sortRecursive($this->comparablePayload($payload));

        return hash('sha256', json_encode($cleanPayload, JSON_UNESCAPED_UNICODE));
    }

    public function signature(string $module, array $payload): string
    {
        $parts = match ($module) {
            'events' => [
                $payload['event_side'] ?? '',
                $payload['event_name'] ?? '',
            ],

            'guests' => [
                $payload['phone'] ?: '',
                $payload['name'] ?? '',
                $payload['event_key'] ?? '',
            ],

            'budget_items' => [
                $payload['event_side'] ?? '',
                $payload['category'] ?? '',
                $payload['item_name'] ?? '',
            ],

            'checklist_items' => [
                $payload['event_side'] ?? '',
                $payload['category'] ?? '',
                $payload['title'] ?? '',
            ],

            default => $payload,
        };

        $normalized = array_map(function ($value) {
            $value = strtolower(trim((string) $value));
            $value = preg_replace('/\s+/', ' ', $value);

            return $value;
        }, $parts);

        return hash('sha256', implode('|', $normalized));
    }

    public function diff(array $sheetPayload, array $webPayload, array $fields): array
    {
        $sheetPayload = $this->comparablePayload($sheetPayload);
        $webPayload = $this->comparablePayload($webPayload);

        $differences = [];

        foreach ($fields as $field) {
            if (in_array($field, $this->ignoredKeys, true)) {
                continue;
            }

            $sheetValue = $sheetPayload[$field] ?? null;
            $webValue = $webPayload[$field] ?? null;

            if ((string) $sheetValue !== (string) $webValue) {
                $differences[$field] = [
                    'sheet' => $sheetValue,
                    'web' => $webValue,
                ];
            }
        }

        return $differences;
    }

    private function sortRecursive(array $payload): array
    {
        ksort($payload);

        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->sortRecursive($value);
            }
        }

        return $payload;
    }
}
