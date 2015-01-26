<?php
namespace rock\helpers;

use League\Flysystem\Util;
use rock\file\FileException;
use rock\log\Log;

/**
 * Helper "File"
 *
 * @package rock\helpers
 */
class FileHelper
{
    const PATTERN_NODIR = 1;
    const PATTERN_ENDSWITH = 4;
    const PATTERN_MUSTBEDIR = 8;
    const PATTERN_NEGATIVE = 16;
    private static $_mimeTypes = [];

    /**
     * Create of file.
     *
     * @param string $pathFile path to file.
     * @param string $value    value.
     * @param int    $const    constant for `file_put_contents`.
     * @param bool   $recursive
     * @param int    $mode     the permission to be set for the created file.
     * @return bool
     */
    public static function create($pathFile, $value = "", $const = 0, $recursive = true, $mode = 0775)
    {
        if ($recursive === true) {
            if (!static::createDirectory(dirname($pathFile))) {
                return false;
            }
        }
        if (!file_put_contents($pathFile, $value, $const)) {
            if (class_exists('\rock\log\Log')) {
                $message = FileException::convertExceptionToString(new FileException(FileException::NOT_CREATE_FILE, ['name' => $pathFile]));
                Log::warn($message);
            }

            return false;
        }
        chmod($pathFile, $mode);

        return true;
    }

    /**
     * Creates a new directory.
     *
     * This method is similar to the PHP `mkdir()` function except that
     * it uses `chmod()` to set the permission of the created directory
     * in order to avoid the impact of the `umask` setting.
     *
     * @param string  $path      path of the directory to be created.
     * @param integer $mode      the permission to be set for the created directory.
     * @param bool    $recursive whether to create parent directories if they do not exist.
     * @return bool whether the directory is created successfully
     */
    public static function createDirectory($path, $mode = 0775, $recursive = true)
    {
        if (is_dir($path)) {
            return true;
        }
        $parentDir = dirname($path);
        if ($recursive && !is_dir($parentDir)) {
            static::createDirectory($parentDir, $mode, true);
        }
        if (!$result = mkdir($path, $mode)) {
            if (class_exists('\rock\log\Log')) {
                $message = FileException::convertExceptionToString(new FileException(FileException::NOT_CREATE_DIR, ['name' => $path]));
                Log::warn($message);
            }
            return false;
        }
        chmod($path, $mode);

        return $result;
    }

    /**
     * Delete of file.
     *
     * @param string $path path to file
     * @return bool
     */
    public static function delete($path)
    {
        if (!file_exists($path)) {
            return false;
        }
        @unlink($path);

        return true;
    }

    /**
     * Rename file.
     *
     * @param string $oldPath old path.
     * @param string $newPath new path.
     * @return bool
     */
    public static function rename($oldPath, $newPath)
    {
        if (!rename($oldPath, $newPath)) {
            if (class_exists('\rock\log\Log')) {
                $message = FileException::convertExceptionToString(new FileHelperException("Error when renaming file: {$oldPath}"));
                Log::err($message);
            }

            return false;
        }

        return true;
    }

    /**
     * Determines the MIME type of the specified file.
     *
     * This method will first try to determine the MIME type based on
     * [finfo_open](http://php.net/manual/en/function.finfo-open.php). If this doesn't work, it will
     * fall back to {@see \rock\helpers\FileHelper::getMimeTypeByExtension()}
     *
     * @param string $file           the file name.
     * @param string $magicFile      name of the optional magic database file, usually something like `/path/to/magic.mime`.
     *                               This will be passed as the second parameter to [finfo_open](http://php.net/manual/en/function.finfo-open.php).
     * @param bool   $checkExtension whether to use the file extension to determine the MIME type in case
     *                               `finfo_open()` cannot determine it.
     * @return string the MIME type (e.g. `text/plain`). Null is returned if the MIME type cannot be determined.
     */
    public static function getMimeType($file, $magicFile = null, $checkExtension = true)
    {
        if (function_exists('finfo_open')) {
            $info = finfo_open(FILEINFO_MIME_TYPE, $magicFile);
            if ($info) {
                $result = finfo_file($info, $file);
                finfo_close($info);
                if ($result !== false) {
                    return $result;
                }
            }
        }

        return $checkExtension ? static::getMimeTypeByExtension($file) : null;
    }

    /**
     * Determines the MIME type based on the extension name of the specified file.
     *
     * This method will use a local map between extension names and MIME types.
     *
     * @param string $file      the file name.
     * @param string $magicFile the path of the file that contains all available MIME type information.
     *                          If this is not set, the default file aliased by `@rock/helpers/mimeTypes.php` will be used.
     * @return string the MIME type. Null is returned if the MIME type cannot be determined.
     */
    public static function getMimeTypeByExtension($file, $magicFile = null)
    {
        $mimeTypes = static::loadMimeTypes($magicFile);
        if (($ext = pathinfo($file, PATHINFO_EXTENSION)) !== '') {
            $ext = strtolower($ext);
            if (isset($mimeTypes[$ext])) {
                return $mimeTypes[$ext];
            }
        }

        return null;
    }

    /**
     * Loads MIME types from the specified file.
     *
     * @param string $magicFile the file that contains MIME type information.
     *                          If null, the file `@rock/helpers/mimeTypes.php` will be used.
     * @return array the mapping from file extensions to MIME types
     */
    protected static function loadMimeTypes($magicFile)
    {
        if ($magicFile === null) {
            $magicFile = __DIR__ . '/mimeTypes.php';
        }
        if (!isset(self::$_mimeTypes[$magicFile])) {
            self::$_mimeTypes[$magicFile] = require($magicFile);
        }

        return self::$_mimeTypes[$magicFile];
    }

    /**
     * Determines the extensions by given MIME type.
     *
     * This method will use a local map between extension names and MIME types.
     *
     * @param string $mimeType  file MIME type.
     * @param string $magicFile the path of the file that contains all available MIME type information.
     *                          If this is not set, the default file aliased by `@rock/helpers/mimeTypes.php` will be used.
     * @return array the extensions corresponding to the specified MIME type
     */
    public static function getExtensionsByMimeType($mimeType, $magicFile = null)
    {
        $mimeTypes = static::loadMimeTypes($magicFile);

        return array_keys($mimeTypes, mb_strtolower($mimeType, 'utf-8'), true);
    }

    /**
     * Copies a whole directory as another one.
     *
     * The files and sub-directories will also be copied over.
     *
     * @param string $src     the source directory
     * @param string $dst     the destination directory
     * @param array  $options options for directory copy. Valid options are:
     *
     * - dirMode: integer, the permission to be set for newly copied directories. Defaults to `0775`.
     * - fileMode:  integer, the permission to be set for newly copied files. Defaults to the current environment setting.
     * - filter: callback, a PHP callback that is called for each directory or file.
     *   The signature of the callback should be: `function ($path)`, where `$path` refers the full path to be filtered.
     *   The callback can return one of the following values:
     *
     *   * true: the directory or file will be copied (the "only" and "except" options will be ignored)
     *   * false: the directory or file will NOT be copied (the "only" and "except" options will be ignored)
     *   * null: the "only" and "except" options will determine whether the directory or file should be copied
     *
     * - only: array, list of patterns that the file paths should match if they want to be copied.
     *   A path matches a pattern if it contains the pattern string at its end.
     *   For example, '.php' matches all file paths ending with '.php'.
     *   Note, the '/' characters in a pattern matches both '/' and '\' in the paths.
     *   If a file path matches a pattern in both "only" and "except", it will NOT be copied.
     * - except: array, list of patterns that the files or directories should match if they want to be excluded from being copied.
     *   A path matches a pattern if it contains the pattern string at its end.
     *   Patterns ending with '/' apply to directory paths only, and patterns not ending with '/'
     *   apply to file paths only. For example, '/a/b' matches all file paths ending with '/a/b';
     *   and '.svn/' matches directory paths ending with '.svn'. Note, the '/' characters in a pattern matches
     *   both '/' and '\' in the paths.
     * - recursive: bool, whether the files under the subdirectories should also be copied. Defaults to true.
     * - beforeCopy: callback, a PHP callback that is called before copying each sub-directory or file.
     *   If the callback returns false, the copy operation for the sub-directory or file will be cancelled.
     *   The signature of the callback should be: `function ($from, $to)`, where `$from` is the sub-directory or
     *   file to be copied from, while `$to` is the copy target.
     * - afterCopy: callback, a PHP callback that is called after each sub-directory or file is successfully copied.
     *   The signature of the callback should be: `function ($from, $to)`, where `$from` is the sub-directory or
     *   file copied from, while `$to` is the copy target.
     */
    public static function copyDirectory($src, $dst, $options = [])
    {
        if (!is_dir($dst)) {
            static::createDirectory($dst, isset($options['dirMode']) ? $options['dirMode'] : 0775, true);
        }
        $handle = opendir($src);
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $from = $src . DIRECTORY_SEPARATOR . $file;
            $to = $dst . DIRECTORY_SEPARATOR . $file;
            if (static::filterPath($from, $options)) {
                if (isset($options['beforeCopy']) && !call_user_func($options['beforeCopy'], $from, $to)) {
                    continue;
                }
                if (is_file($from)) {
                    copy($from, $to);
                    if (isset($options['fileMode'])) {
                        @chmod($to, $options['fileMode']);
                    }
                } else {
                    static::copyDirectory($from, $to, $options);
                }
                if (isset($options['afterCopy'])) {
                    call_user_func($options['afterCopy'], $from, $to);
                }
            }
        }
        closedir($handle);
    }

    /**
     * Checks if the given file path satisfies the filtering options.
     *
     * @param string $path    the path of the file or directory to be checked
     * @param array  $options the filtering options.
     * @return bool whether the file or directory satisfies the filtering options.
     */
    public static function filterPath($path, $options)
    {
        if (isset($options['filter'])) {
            $result = call_user_func($options['filter'], $path);
            if (is_bool($result)) {
                return $result;
            }
        }
        if (empty($options['except']) && empty($options['only'])) {
            return true;
        }
        $path = str_replace('\\', '/', $path);
        if (!empty($options['except'])) {
            if (($except = self::lastExcludeMatchingFromList($options['basePath'], $path, $options['except'])) !== null
            ) {
                return $except['flags'] & self::PATTERN_NEGATIVE;
            }
        }
        if (!is_dir($path) && !empty($options['only'])) {
            if (($except = self::lastExcludeMatchingFromList($options['basePath'], $path, $options['only'])) !== null) {
                // don't check PATTERN_NEGATIVE since those entries are not prefixed with !
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Scan the given exclude list in reverse to see whether pathname
     * should be ignored.  The first match (i.e. the last on the list), if
     * any, determines the fate.  Returns the element which
     * matched, or null for undecided.
     *
     * Based on `last_exclude_matching_from_list()` from dir.c of git 1.8.5.3 sources.
     *
     * @param string $basePath
     * @param string $path
     * @param array  $excludes list of patterns to match $path against
     * @return string null or one of $excludes item as an array with keys: 'pattern', 'flags'
     * @throws \Exception if any of the exclude patterns is not a string or an array with keys: pattern, flags, firstWildcard.
     */
    private static function lastExcludeMatchingFromList($basePath, $path, $excludes)
    {
        foreach (array_reverse($excludes) as $exclude) {
            if (is_string($exclude)) {
                $exclude = self::parseExcludePattern($exclude);
            }
            if (!isset($exclude['pattern']) || !isset($exclude['flags']) || !isset($exclude['firstWildcard'])) {
                throw new \Exception(
                    'If exclude/include pattern is an array it must contain the pattern, flags and firstWildcard keys.');
            }
            if ($exclude['flags'] & self::PATTERN_MUSTBEDIR && !is_dir($path)) {
                continue;
            }
            if ($exclude['flags'] & self::PATTERN_NODIR) {
                if (self::matchBasename(
                    basename($path),
                    $exclude['pattern'],
                    $exclude['firstWildcard'],
                    $exclude['flags'])
                ) {
                    return $exclude;
                }
                continue;
            }
            if (self::matchPathname(
                $path,
                $basePath,
                $exclude['pattern'],
                $exclude['firstWildcard'],
                $exclude['flags'])
            ) {
                return $exclude;
            }
        }

        return null;
    }

    /**
     * Processes the pattern, stripping special characters like / and ! from the beginning and settings flags instead.
     *
     * @param string $pattern
     * @return array with keys: (string)pattern, (int)flags, (int|bool)firstWildcard
     * @throws FileException if the pattern is not a string.
     */
    private static function parseExcludePattern($pattern)
    {
        if (!is_string($pattern)) {
            throw new FileException('Exclude/include pattern must be a string.');
        }
        $result = array(
            'pattern' => $pattern,
            'flags' => 0,
            'firstWildcard' => false,
        );
        if (!isset($pattern[0])) {
            return $result;
        }
        if ($pattern[0] == '!') {
            $result['flags'] |= self::PATTERN_NEGATIVE;
            $pattern = StringHelper::byteSubstr($pattern, 1, StringHelper::byteLength($pattern));
        }
        $len = StringHelper::byteLength($pattern);
        if ($len && StringHelper::byteSubstr($pattern, -1, 1) == '/') {
            $pattern = StringHelper::byteSubstr($pattern, 0, -1);
            $len--;
            $result['flags'] |= self::PATTERN_MUSTBEDIR;
        }
        if (strpos($pattern, '/') === false) {
            $result['flags'] |= self::PATTERN_NODIR;
        }
        $result['firstWildcard'] = self::firstWildcardInPattern($pattern);
        if ($pattern[0] == '*' &&
            self::firstWildcardInPattern(StringHelper::byteSubstr($pattern, 1, StringHelper::byteLength($pattern))) === false
        ) {
            $result['flags'] |= self::PATTERN_ENDSWITH;
        }
        $result['pattern'] = $pattern;

        return $result;
    }

    /**
     * Searches for the first wildcard character in the pattern.
     *
     * @param string $pattern the pattern to search in
     * @return integer|bool position of first wildcard character or false if not found
     */
    private static function firstWildcardInPattern($pattern)
    {
        $wildcards = array('*', '?', '[', '\\');
        $wildcardSearch = function ($r, $c) use ($pattern) {
            $p = strpos($pattern, $c);

            return $r === false ? $p : ($p === false ? $r : min($r, $p));
        };

        return array_reduce($wildcards, $wildcardSearch, false);
    }

    /**
     * Performs a simple comparison of file or directory names.
     *
     * Based on `match_basename()` from dir.c of git 1.8.5.3 sources.
     *
     * @param string       $baseName      file or directory name to compare with the pattern
     * @param string       $pattern       the pattern that $baseName will be compared against
     * @param integer|bool $firstWildcard location of first wildcard character in the $pattern
     * @param integer      $flags         pattern flags
     * @return bool wheter the name matches against pattern
     */
    private static function matchBasename($baseName, $pattern, $firstWildcard, $flags)
    {
        if ($firstWildcard === false) {
            if ($pattern === $baseName) {
                return true;
            }
        } else if ($flags & self::PATTERN_ENDSWITH) {
            /* "*literal" matching against "fooliteral" */
            $n = StringHelper::byteLength($pattern);
            if (StringHelper::byteSubstr($pattern, 1, $n) === StringHelper::byteSubstr($baseName, -$n, $n)) {
                return true;
            }
        }

        return fnmatch($pattern, $baseName, 0);
    }

    /**
     * Compares a path part against a pattern with optional wildcards.
     *
     * Based on `match_pathname()` from dir.c of git 1.8.5.3 sources.
     *
     * @param string       $path          full path to compare
     * @param string       $basePath      base of path that will not be compared
     * @param string       $pattern       the pattern that path part will be compared against
     * @param integer|bool $firstWildcard location of first wildcard character in the $pattern
     * @param integer      $flags         pattern flags
     * @return bool wheter the path part matches against pattern
     */
    private static function matchPathname($path, $basePath, $pattern, $firstWildcard, $flags)
    {
        // match with FNM_PATHNAME; the pattern has base implicitly in front of it.
        if (isset($pattern[0]) && $pattern[0] == '/') {
            $pattern = StringHelper::byteSubstr($pattern, 1, StringHelper::byteLength($pattern));
            if ($firstWildcard !== false && $firstWildcard !== 0) {
                $firstWildcard--;
            }
        }
        $namelen = StringHelper::byteLength($path) - (empty($basePath) ? 0 : StringHelper::byteLength($basePath) + 1);
        $name = StringHelper::byteSubstr($path, -$namelen, $namelen);
        if ($firstWildcard !== 0) {
            if ($firstWildcard === false) {
                $firstWildcard = StringHelper::byteLength($pattern);
            }
            // if the non-wildcard part is longer than the remaining pathname, surely it cannot match.
            if ($firstWildcard > $namelen) {
                return false;
            }
            if (strncmp($pattern, $name, $firstWildcard)) {
                return false;
            }
            $pattern = StringHelper::byteSubstr($pattern, $firstWildcard, StringHelper::byteLength($pattern));
            $name = StringHelper::byteSubstr($name, $firstWildcard, $namelen);
            // If the whole pattern did not have a wildcard, then our prefix match is all we need; we do not need to call fnmatch at all.
            if (empty($pattern) && empty($name)) {
                return true;
            }
        }

        return fnmatch($pattern, $name, FNM_PATHNAME);
    }

    /**
     * Removes a directory (and all its content) recursively.
     *
     * @param string $dir the directory to be deleted recursively.
     * @return bool
     */
    public static function deleteDirectory($dir)
    {
        if (!is_dir($dir) || !($handle = opendir($dir))) {
            return false;
        }
        //$dir =dirname($dir);
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                unlink($path);
            } else {
                static::deleteDirectory($path);
            }
        }
        closedir($handle);
        rmdir($dir);

        return true;
    }

    /**
     * Normalizes a file/directory path.
     *
     * After normalization, the directory separators in the path will be `DIRECTORY_SEPARATOR`,
     * and any trailing directory separators will be removed. For example, `/home\demo/` on Linux
     * will be normalized as '/home/demo'.
     *
     * @param string $path the file/directory path to be normalized
     * @param string $ds   the directory separator to be used in the normalized result. Defaults to `DIRECTORY_SEPARATOR`.
     * @return string the normalized file/directory path
     */
    public static function normalizePath($path, $ds = DIRECTORY_SEPARATOR)
    {
        return rtrim(strtr($path, ['/' => $ds, '\\' => $ds]), $ds);
    }

    /**
     * Returns the trailing name component of a path.
     *
     * This method is similar to the php function `basename()` except that it will
     * treat both `\` and `/` as directory separators, independent of the operating system.
     * This method was mainly created to work on php namespaces. When working with real
     * file paths, php's `basename()` should work fine for you.
     * Note: this method is not aware of the actual filesystem, or path components such as `..`.
     *
     * @param string $path   A path string.
     * @param string $suffix If the name component ends in suffix this will also be cut off.
     * @return string the trailing name component of the given path.
     * @see http://www.php.net/manual/en/function.basename.php
     */
    public static function basename($path, $suffix = '')
    {
        if (($len = mb_strlen($suffix)) > 0 && mb_substr($path, -$len) == $suffix) {
            $path = mb_substr($path, 0, -$len);
        }
        $path = rtrim(str_replace('\\', '/', $path), '/\\');
        if (($pos = mb_strrpos($path, '/')) !== false) {
            return mb_substr($path, $pos + 1);
        }

        return $path;
    }

    /**
     * Returns parent directory's path.
     *
     * This method is similar to `dirname()` except that it will treat
     * both `\` and `/` as directory separators, independent of the operating system.
     *
     * @param string $path A path string.
     * @return string the parent directory's path.
     * @see http://www.php.net/manual/en/function.basename.php
     */
    public static function dirname($path)
    {
        $pos = mb_strrpos(str_replace('\\', '/', $path), '/');
        if ($pos !== false) {
            return mb_substr($path, 0, $pos);
        } else {
            return '';
        }
    }

    /**
     * Converts php.ini style size to bytes.
     *
     * @param string $sizeStr
     * @return int
     */
    public static function sizeToBytes($sizeStr)
    {
        if (!is_string($sizeStr)) {
            return $sizeStr;
        }
        switch (substr($sizeStr, -1)) {
            case 'M':
            case 'm':
                return (int)$sizeStr * 1048576;
            case 'K':
            case 'k':
                return (int)$sizeStr * 1024;
            case 'G':
            case 'g':
                return (int)$sizeStr * 1073741824;
            default:
                return (int)$sizeStr;
        }
    }

    /**
     * Fix for overflowing signed 32 bit integers,
     * works for sizes up to 2^32-1 bytes (4 GiB - 1).
     *
     * @param int $size
     * @return float
     */
    public static function fixedIntegerOverflow($size)
    {
        if ($size < 0) {
            $size += 2.0 * (PHP_INT_MAX + 1);
        }

        return $size;
    }
}