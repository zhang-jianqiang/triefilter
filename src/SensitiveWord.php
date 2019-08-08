<?php

namespace Zjq;

/**
 * 敏感词过滤
 */
class SensitiveWord
{
    public function __construct()
    {
        if (!function_exists('trie_filter_new')) {
            throw new \Exception('缺少trie_filter扩展');
        }
    }

    public function load_sensitive_word_dict()
    {
        $dict_path = __DIR__ . '/invalid.txt';
        // 判断路径是否存在
        if (!is_file($dict_path)) {
            return false;
        }
        $arrWord = file($dict_path);

        // 创建一个空的trie tree
        $resTrie = trie_filter_new();
        //向trie tree中添加敏感词
        foreach ($arrWord as $k => $v) {
            trie_filter_store($resTrie, $v);
        }
        //生成敏感词文件
        trie_filter_save($resTrie, __DIR__ .'/invalid.tree');

        if (!is_file(__DIR__ . '/invalid.tree')) {
            return false;
        }

        return true;
    }

    public function filter_sensitive_word($str)
    {
        if (!file_exists(__DIR__ .'/invalid.tree')) {
            $this->load_sensitive_word_dict();
        }
        // 加载敏感词
        $resTrie = trie_filter_load(__DIR__ . '/invalid.tree');
        //在文本中查找所有的脏字
        $arrRet = trie_filter_search_all($resTrie, $str);
        // 把脏字提取出来
        $sensitiveWord = [];
        foreach ($arrRet as $v) {
            $sensitiveWord[] = substr($str, $v[0], $v[1]);
        }
        return $sensitiveWord;
    }

    public function replace_sensitive_word($str, $replace_char = '*')
    {
        if (!file_exists(__DIR__ .'/invalid.tree')) {
            $this->load_sensitive_word_dict();
        }
        // 加载敏感词
        $resTrie = trie_filter_load(__DIR__ . '/invalid.tree');
        //在文本中查找所有的脏字
        $arrRet = trie_filter_search_all($resTrie, $str);
        // 把脏字提取出来
        $sensitiveWord = [];
        foreach ($arrRet as $v) {
            $sensitiveWord[] = substr($str, $v[0], $v[1]);
        }
        foreach ($sensitiveWord as $word) {
            $str = str_replace($word, str_repeat($replace_char, mb_strlen($word)), $str);
        }
        return $str;
    }
}