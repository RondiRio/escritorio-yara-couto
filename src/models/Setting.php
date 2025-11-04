<?php

namespace Models;

use Core\Model;

/**
 * Model Setting - Gerencia configurações do site
 */
class Setting extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $fillable = [
        'key',
        'value',
        'type',
        'group'
    ];
    protected $timestamps = true;

    /**
     * Busca valor de uma configuração
     */
    public function get($key, $default = null)
    {
        $setting = $this->whereOne('key', '=', $key);
        
        if (!$setting) {
            return $default;
        }

        return $this->castValue($setting['value'], $setting['type']);
    }

    /**
     * Define valor de uma configuração
     */
    public function set($key, $value, $type = 'string', $group = 'general')
    {
        $existing = $this->whereOne('key', '=', $key);

        if ($existing) {
            return $this->update($existing['id'], [
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]);
        }

        return $this->create([
            'key' => $key,
            'value' => $value,
            'type' => $type,
            'group' => $group
        ]);
    }

    /**
     * Busca configurações por grupo
     */
    public function getByGroup($group)
    {
        $settings = $this->where('group', '=', $group);
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['key']] = $this->castValue($setting['value'], $setting['type']);
        }

        return $result;
    }

    /**
     * Busca todas as configurações como array key => value
     */
    public function getAllSettings()
    {
        $settings = $this->all();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['key']] = $this->castValue($setting['value'], $setting['type']);
        }

        return $result;
    }

    /**
     * Atualiza múltiplas configurações
     */
    public function updateMultiple($settings)
    {
        foreach ($settings as $key => $value) {
            $existing = $this->whereOne('key', '=', $key);
            
            if ($existing) {
                $this->update($existing['id'], ['value' => $value]);
            }
        }

        return true;
    }

    /**
     * Converte valor conforme tipo
     */
    private function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            
            case 'integer':
                return (int)$value;
            
            case 'float':
                return (float)$value;
            
            case 'json':
                return json_decode($value, true);
            
            case 'array':
                return explode(',', $value);
            
            default:
                return $value;
        }
    }

    /**
     * Remove uma configuração
     */
    public function remove($key)
    {
        $setting = $this->whereOne('key', '=', $key);
        
        if ($setting) {
            return $this->delete($setting['id']);
        }

        return false;
    }

    /**
     * Verifica se configuração existe
     */
    public function has($key)
    {
        return $this->exists('key', $key);
    }

    /**
     * Carrega configurações na sessão (cache)
     */
    public function loadToSession()
    {
        $_SESSION['site_settings'] = $this->getAllSettings();
    }

    /**
     * Busca configuração da sessão
     */
    public function getFromSession($key, $default = null)
    {
        if (isset($_SESSION['site_settings'][$key])) {
            return $_SESSION['site_settings'][$key];
        }

        return $default;
    }

    /**
     * Busca configurações do site
     */
    public function getSiteInfo()
    {
        return [
            'site_name' => $this->get('site_name', 'Escritório Yara Couto Vitoria'),
            'site_description' => $this->get('site_description', 'Advocacia Previdenciária'),
            'site_email' => $this->get('site_email', 'contato@escritorioyara.com.br'),
            'site_phone' => $this->get('site_phone', ''),
            'site_whatsapp' => $this->get('site_whatsapp', ''),
            'site_address' => $this->get('site_address', ''),
            'oab_number' => $this->get('oab_number', ''),
            'oab_state' => $this->get('oab_state', '')
        ];
    }

    /**
     * Busca configurações de SEO
     */
    public function getSEOSettings()
    {
        return [
            'meta_title' => $this->get('meta_title', ''),
            'meta_description' => $this->get('meta_description', ''),
            'meta_keywords' => $this->get('meta_keywords', ''),
            'google_analytics' => $this->get('google_analytics', ''),
            'facebook_pixel' => $this->get('facebook_pixel', '')
        ];
    }

    /**
     * Busca configurações de redes sociais
     */
    public function getSocialMedia()
    {
        return [
            'facebook_url' => $this->get('facebook_url', ''),
            'instagram_url' => $this->get('instagram_url', ''),
            'linkedin_url' => $this->get('linkedin_url', ''),
            'youtube_url' => $this->get('youtube_url', '')
        ];
    }
}