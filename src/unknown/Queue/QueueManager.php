<?php

namespace unknown\Queue;

use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use unknown\Loader;

class  QueueManager
{
    public array $queues = [];

    /**
     * @param array $queues
     */
    public function addQueue(string $PName, string $modality): void
    {
        if (!in_array($PName, $this->queues)) {
            $this->queues[] = [
                'PName' => $PName,
                'Position' => $this->getLastPosition() + 1,
                'Modality' => $modality
            ];
        }
    }

    public function removeQueue(string $PName): void
    {
        if (($key = array_search($PName, $this->queues)) !== false) {
            unset($this->queues[$key]);
        }
    }

    public function inQueue(string $PName): bool{
        return in_array($PName, $this->queues);
    }

    public function getLastPosition(): ?int
    {
        $last = end($this->queues);
        return $last['Position'] ?? 0;
    }

    public function getQueues(): array
    {
        return $this->queues;

    }

    public function Translate(string $PName): bool
    {
        foreach ($this->queues as $queue) {
            if ($queue['PName'] === $PName) {
                $modality = strtolower($queue['Modality']);

                if ($modality !== null && isset($this->modalityServers[$modality])) {
                    $serverInfo = $this->modalityServers[$modality];
                    $player = Server::getInstance()->getPlayerExact($PName);

                    if ($player instanceof Player) {
                        $config = Loader::getInstance()->getConfig();
                        $selections = [
                            'hcf' => ['ip' => $config['modalitys']['hcf']['ip'], 'port' => $config['hcf']['port']],
                            'kitmap' =>  ['ip' => $config['modalitys']['kitmap']['ip'], 'port' => $config['kitmap']['port']],
                            'practice' =>  ['ip' => $config['modalitys']['practice']['ip'], 'port' => $config['practice']['port']],
                        ];

                        $player->transfer($selections[$modality]['ip'], $selections[$modality]['port']);
                        return true;
                    }
                }
            }
        }

        return false;
    }
}