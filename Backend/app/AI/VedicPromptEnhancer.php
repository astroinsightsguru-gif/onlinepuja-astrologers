<?php

namespace App\AI;

class VedicPromptEnhancer
{
    /**
     * Presets for authentic Indian Vedic ceremonies, festivals, and astrology
     */
    protected static array $culturalPresets = [
        'griha pravesh' => [
            'subject' => 'Authentic traditional Indian Griha Pravesh housewarming puja ceremony. Decorated home threshold with fresh green mango leaves (aam ke patte) toran and fragrant yellow-orange marigold flower garlands, intricate geometric white and vermilion rice flour rangoli on clean floor. A gleaming sacred brass Kalash filled with holy Ganga water, topped with fresh mango leaves and a sacred coconut tied with red Kalawa mauli thread. In the background, an authentic traditional copper havan kund with glowing sacred wood embers and a gentle wisp of holy havan smoke.',
            'mood' => 'Auspicious, serene, warm morning temple sunlight streaming through the doorway, photorealistic documentary photography, shot on Hasselblad H6D-100c, 85mm f/1.8 lens, natural depth of field, authentic Indian culture, true-to-life colors, 8k uhd, editorial quality.'
        ],
        'satyanarayan' => [
            'subject' => 'Traditional Hindu Shri Satyanarayan Katha puja altar. Altar decorated with fresh green banana leaves, sacred brass Kalash with coconut and mango leaves, authentic Shaligram stone adorned with fragrant tulsi leaves and yellow champa flowers. Wooden chowki draped in pristine red silk cloth, brass panchapatra, agarbatti incense stand with rising aromatic smoke, and sweet prasad offerings in silver katori.',
            'mood' => 'Spiritual serenity, warm glowing brass diya lamps, documentary realism, 85mm lens portrait, sharp focus on sacred puja samagri, natural textures, no CGI.'
        ],
        'shiva' => [
            'subject' => 'Ancient sacred black stone Shiva Lingam in a tranquil stone temple sanctum sanctorum. Adorned with fresh green bilva (bael) leaves, fragrant white datura flowers, and cooling chandan sandalwood paste. A slow sacred abhishek stream of holy water gently trickles over the lingam from a suspended antique brass vessel. Traditional brass oil lamps (kuthuvilakku) flickering softly.',
            'mood' => 'Deep meditative stillness, aromatic dhoop smoke, soft golden chiaroscuro lighting, authentic Indian temple heritage, hyper-detailed photography, 8k.'
        ],
        'ganesh' => [
            'subject' => 'Sacred handcrafted brass Lord Ganesha idol gracefully adorned with fresh red hibiscus flowers and auspicious green durva grass. Placed on an intricately carved wooden singhasan covered in royal saffron fabric. Burning brass oil lamp with warm flickering cotton flame, silver thali with freshly prepared steamed modaks, fresh coconut, and kumkum tilak.',
            'mood' => 'Divine warmth, festive joy, soft temple bokeh, documentary cultural photography, high-definition textures of brass and fresh flower petals.'
        ],
        'lakshmi' => [
            'subject' => 'Traditional Diwali Lakshmi Puja sacred setting inside an Indian heritage home. Handcrafted terracotta clay diyas with real flickering mustard oil cotton flames, radiant floral rangoli made of fresh orange marigold and red rose petals. Auspicious silver coins engraved with Goddess Lakshmi, gleaming brass puja thali with haldi, kumkum, akshat rice, and traditional sweets.',
            'mood' => 'Festive auspicious warmth, soft candlelight glow, cinematic shallow depth of field, authentic Indian family celebration, award-winning photography.'
        ],
        'havan' => [
            'subject' => 'Sacred traditional Vedic Yagya Havan ceremony. Authentic stepped copper havan kund with dried sacred mango wood, fragrant dried herbs, and pure cow ghee offering creating a radiant, controlled sacred fire with soft holy smoke. Knowledgeable Vedic pandit in pitambar dhoti and angavastram offering samagri with folded devotion.',
            'mood' => 'Vedic authenticity, crackling sacred fire embers, warm golden illumination, cinematic documentary, photorealistic Indian spiritual ritual.'
        ],
        'kundli' => [
            'subject' => 'Traditional Vedic astrology Jyotish consultation desk. Ancient handmade parchment paper displaying an authentic hand-drawn Vedic birth chart (Janampatri Kundli) in Sanskrit calligraphy. An antique brass celestial armillary sphere, genuine certified gemstones (natural unheated Ruby, Yellow Sapphire, Emerald) resting on raw silk, and a glowing brass oil lamp illuminating the astrological calculations.',
            'mood' => 'Scholarly wisdom, mystical ancient knowledge, macro photography, rich wood and paper textures, cinematic lighting.'
        ],
    ];

    /**
     * Negative prompts to strictly eliminate cartoonish, CGI, or plastic AI output
     */
    public const NEGATIVE_PROMPT = '3d render, cartoon, anime, illustration, cgi, plastic toy, doll, miniature, golden figurine, abstract deity, glowing neon laser, fantasy orb, disfigured hands, extra fingers, deformed limbs, blurry, low resolution, oversaturated orange filter, fake, uncanny valley, westernized, digital painting artifacts, text watermark, logo, signature, modern sunglasses, cheap rendering';

    /**
     * Enhance any raw prompt/festival into a culturally accurate, photorealistic masterpiece
     */
    public static function enhance(string $rawTopic, string $style = 'photorealistic', string $aspectRatio = '1:1'): array
    {
        $lower = strtolower($rawTopic);
        $preset = null;

        foreach (self::$culturalPresets as $key => $data) {
            if (str_contains($lower, $key)) {
                $preset = $data;
                break;
            }
        }

        if (!$preset) {
            // General Vedic ceremony fallback
            $preset = [
                'subject' => "Authentic traditional Vedic Hindu spiritual ritual for {$rawTopic}. Sanctified temple mandap with sacred brass Kalash topped with fresh green mango leaves and sacred coconut tied with red mauli thread, fresh fragrant marigold flower garlands, and glowing traditional brass oil diyas with real warm flames.",
                'mood' => 'Devotional serenity, soft morning sunlight, documentary photography, shot on Hasselblad 85mm f/1.8, authentic Indian cultural heritage, realistic textures, 8k.'
            ];
        }

        // Apply visual style modifiers
        switch ($style) {
            case 'heritage_art':
                $styleSuffix = 'Classical Indian fine art, Raja Ravi Varma style oil on canvas painting, rich heritage color palette, subtle antique canvas texture, elegant traditional royal composition.';
                break;
            case 'temple_sanctum':
                $styleSuffix = 'Deep temple sanctum sanctorum (garbhagriha), atmospheric chiaroscuro lighting, dramatic shadows illuminated by pure ghee deepam lamps, rising aromatic dhoop incense smoke, quiet spiritual awe.';
                break;
            case 'video_reel':
                $styleSuffix = 'Cinematic 9:16 vertical motion reel, gentle camera slow push-in, subtle flickering diya flame, drifting incense smoke, sacred temple atmosphere, photorealistic 4k.';
                break;
            case 'photorealistic':
            default:
                $styleSuffix = 'Photorealistic documentary photography, National Geographic culture series, Hasselblad H6D-100c, 85mm f/1.8 lens, natural lighting, ultra-sharp focus on brass and flowers, zero cartoon CGI.';
                break;
        }

        $fullPrompt = trim("{$preset['subject']} {$preset['mood']} {$styleSuffix}");

        return [
            'prompt'          => $fullPrompt,
            'negative_prompt' => self::NEGATIVE_PROMPT,
            'style'           => $style,
            'aspect_ratio'    => $aspectRatio,
            'size'            => self::resolveSize($aspectRatio),
        ];
    }

    /**
     * Resolve pixel dimensions based on aspect ratio
     */
    public static function resolveSize(string $aspectRatio): string
    {
        return match ($aspectRatio) {
            '4:5'   => '1024x1280',
            '9:16'  => '1024x1792',
            '16:9'  => '1792x1024',
            default => '1024x1024',
        };
    }
}
