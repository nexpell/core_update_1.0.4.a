<?php
declare(strict_types=1);

class TeamSpeakHtmlRendererWidget
{

    protected array $cfg;

    public function __construct(array $cfg = [])
    {
        $this->cfg = $cfg;
    }



    public static function render(array $tree): string
    {
        $html = '<div class="ts-tree">';
        foreach ($tree as $channel) {
            $html .= self::renderChannelWidget($channel);
        }
        $html .= '</div>';
        return $html;
    }

    private static function renderChannelWidget(array $ch, int $level = 0): string
    {
        
        $indent = $level * 20;
        $html   = '';

        /* ==========================
           📁 CHANNEL
        ========================== */
        $html .= '<div class="ts-channel" style="margin-left:' . $indent . 'px">';
        $html .= '<div class="ts-row">';

        /* ==========================
           LINKS: ORDNER + NAME
        ========================== */

        $html .= '<div class="ts-folder-icon-wrap">';

        /* 📁 Icon nur bei echten Voice-Channels */
        if (empty($ch['is_text'])) {
            $folderIcon  = !empty($ch['locked']) ? 'bi-folder-x' : 'bi-folder-check';
            $folderClass = !empty($ch['locked']) ? 'text-danger' : 'text-success';

            $html .= '<i class="bi ' . $folderIcon . ' ' . $folderClass . ' ts-folder-icon"></i>';
        }

        $html .= '<span class="ts-folder-name">';
        $html .= htmlspecialchars((string)$ch['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $html .= '</span>';

        $html .= '</div>';


        /* ==========================
           RECHTS: NUR STANDARD-CHANNEL
        ========================== */
        $html .= '<div class="ts-channel-icons">';

        if (!empty($ch['default'])) {
            $html .= '<i class="bi bi-house-fill text-info" title="Standard-Channel"></i>';
        }

        $html .= '</div>'; // ts-channel-icons
        $html .= '</div>'; // ts-row


        /* ==========================
           👤 CLIENTS
        ========================== */
        foreach ($ch['clients'] ?? [] as $cl) {

            $micMuted =
                !empty($cl['client_input_muted']) ||
                !empty($cl['client_input_hardware']);

            $html .= '<div class="ts-client" style="margin-left:' . ($indent + 20) . 'px">';
            $html .= '<div class="ts-row">';

            /* LEFT */
            $html .= '<div class="ts-left"><small>';

            /* Nickname */
            $html .= htmlspecialchars($cl['nickname']);

            /* Talking > Hardware-Mute > Ready */
            if (!empty($cl['client_flag_talking'])) {
                $html .= '<i class="bi bi-mic-fill ms-1 text-success" title="Mikro bereit"></i>';
            } elseif (!empty($cl['client_input_muted'])) {
                $html .= '<i class="bi bi-mic-mute-fill ms-1 text-danger" title="Mikro stumm"></i>';    
            } elseif (!empty($cl['client_input_hardware'])) {
                $html .= '<i class="bi bi-mic-fill ms-1 text-success" title="Mikro bereit"></i>';
            } else {
                $html .= '<i class="bi bi-mic ms-1 text-muted" title="Mikro bereit"></i>';
            }

            /* Sound stumm */
            if (!empty($cl['client_output_muted'])) {
                // TS-intern gemutet
                $html .= '<i class="bi bi-volume-mute-fill ms-1 text-danger" style="font-size:1.55em" title="Sound stumm"></i>';
            }


            /* AFK */
            if (!empty($cl['away'])) {
                $html .= '<i class="bi bi-moon-fill ms-1 text-warning" title="AFK"></i>';
            }

            $html .= '</small></div>';

            $html .= '</div>';

            $html .= '</div>';

        }

        foreach ($ch['children'] ?? [] as $child) {
            $html .= self::renderChannelWidget($child, $level + 1);
        }

        $html .= '</div>';
        return $html;
    }
}

