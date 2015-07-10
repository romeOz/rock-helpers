<?php

namespace rockunit;


use rock\helpers\FileHelper;
use rockunit\common\CommonTestTrait;

class FileHelperTest extends \PHPUnit_Framework_TestCase
{
    use CommonTestTrait;


    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::clearRuntime();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        static::clearRuntime();
    }


    public function testCreate()
    {
        $this->assertTrue(FileHelper::create(ROCKUNIT_RUNTIME . '/cache/file.tmp', 'text...'));
        $this->assertFalse(FileHelper::create(ROCKUNIT_RUNTIME . '/cache/file.tmp'));
    }

    /**
     * @depends testCreate
     */
    public function testBasename()
    {
        $this->assertSame('file.tmp', FileHelper::basename(ROCKUNIT_RUNTIME . '/cache\\file.tmp'));
        $this->assertSame('file', FileHelper::basename(ROCKUNIT_RUNTIME . '/cache/file.tmp', '.tmp'));
    }

    /**
     * @depends testCreate
     */
    public function testDirname()
    {
        $this->assertSame(ROCKUNIT_RUNTIME . '/cache', FileHelper::dirname(ROCKUNIT_RUNTIME . '\\cache\\file.tmp'));
        $this->assertSame('', FileHelper::dirname('file.tmp'));
    }

    public function testGetMimeTypeByExtension()
    {
        $this->assertTrue(FileHelper::create(ROCKUNIT_RUNTIME . '/cache/file.doc', 'text'));
        $this->assertSame('application/msword', FileHelper::getMimeTypeByExtension(ROCKUNIT_RUNTIME . '/cache/file.doc'));
    }

    /**
     * @depends testGetMimeTypeByExtension
     */
    public function testDelete()
    {
        $this->assertTrue(FileHelper::delete(ROCKUNIT_RUNTIME . '/cache/file.doc'));
        $this->assertFalse(FileHelper::delete(ROCKUNIT_RUNTIME . '/cache/file.doc'));
    }

    /**
     * @depends testCreate
     */
    public function testRename()
    {
        $this->assertFalse(FileHelper::rename(ROCKUNIT_RUNTIME . '/cache/new_file.tmp', ROCKUNIT_RUNTIME . '/cache/file.tmp'));
        $this->assertTrue(FileHelper::rename(ROCKUNIT_RUNTIME . '/cache/file.tmp', ROCKUNIT_RUNTIME . '/cache/new_file.tmp'));
    }


    /**
     * @depends testRename
     */
    public function testGetMimeType()
    {
        $this->assertSame('inode/x-empty', FileHelper::getMimeType(ROCKUNIT_RUNTIME . '/cache/new_file.tmp'));
        $this->assertFalse(FileHelper::getMimeType(ROCKUNIT_RUNTIME . '/cache/file.tmp'));
    }

    public function testGetExtensionsByMimeType()
    {
        $this->assertNotEmpty(FileHelper::getExtensionsByMimeType('application/zip'));
    }

    /**
     * @depends testCreate
     */
    public function testCopyDirectory()
    {
        $this->assertFalse(is_dir(ROCKUNIT_RUNTIME . '/cache_copy'));
        FileHelper::copyDirectory(ROCKUNIT_RUNTIME . '/cache/', ROCKUNIT_RUNTIME . '/cache_copy');
        $this->assertTrue(is_dir(ROCKUNIT_RUNTIME . '/cache_copy'));
    }

    /**
     * @depends testCopyDirectory
     */
    public function testDeleteDirectory()
    {
        $this->assertTrue(FileHelper::deleteDirectory(ROCKUNIT_RUNTIME));
        $this->assertFalse(is_dir(ROCKUNIT_RUNTIME . '/cache_copy'));
        $this->assertFalse(FileHelper::deleteDirectory(ROCKUNIT_RUNTIME));
    }

    public function testNormalizePath()
    {
        $ds = DIRECTORY_SEPARATOR;
        $this->assertEquals("{$ds}a{$ds}b", FileHelper::normalizePath('//a\b/'));
        $this->assertEquals("{$ds}b{$ds}c", FileHelper::normalizePath('/a/../b/c'));
        $this->assertEquals("{$ds}c", FileHelper::normalizePath('/a\\b/../..///c'));
        $this->assertEquals("{$ds}c", FileHelper::normalizePath('/a/.\\b//../../c'));
        $this->assertEquals("c", FileHelper::normalizePath('/a/.\\b/../..//../c'));
        $this->assertEquals("..{$ds}c", FileHelper::normalizePath('//a/.\\b//..//..//../../c'));

        // relative paths
        $this->assertEquals(".", FileHelper::normalizePath('.'));
        $this->assertEquals(".", FileHelper::normalizePath('./'));
        $this->assertEquals("a", FileHelper::normalizePath('.\\a'));
        $this->assertEquals("a{$ds}b", FileHelper::normalizePath('./a\\b'));
        $this->assertEquals(".", FileHelper::normalizePath('./a\\../'));
        $this->assertEquals("..{$ds}..{$ds}a", FileHelper::normalizePath('../..\\a'));
        $this->assertEquals("..{$ds}..{$ds}a", FileHelper::normalizePath('../..\\a/../a'));
        $this->assertEquals("..{$ds}..{$ds}b", FileHelper::normalizePath('../..\\a/../b'));
        $this->assertEquals("..{$ds}a", FileHelper::normalizePath('./..\\a'));
        $this->assertEquals("..{$ds}a", FileHelper::normalizePath('././..\\a'));
        $this->assertEquals("..{$ds}a", FileHelper::normalizePath('./..\\a/../a'));
        $this->assertEquals("..{$ds}b", FileHelper::normalizePath('./..\\a/../b'));
        $this->assertEquals(".{$ds}..{$ds}a{$ds}..{$ds}a", FileHelper::normalizePath('./..\\a/../a', DIRECTORY_SEPARATOR, false));
    }

    public function testSizeToBytes()
    {
        $this->assertSame(5120, FileHelper::sizeToBytes('5K'));
        $this->assertSame(5242880, FileHelper::sizeToBytes('5m'));
        $this->assertSame(5368709120, FileHelper::sizeToBytes('5G'));
        $this->assertSame(1024, FileHelper::sizeToBytes(1024));
        $this->assertSame(1024, FileHelper::sizeToBytes('1024'));
    }

    public function testCopyDirectory2()
    {
        $srcDirName = 'test_src_dir';
        $files = [
            'file1.txt' => 'file 1 content',
            'file2.txt' => 'file 2 content',
        ];
        $this->createFileStructure([
            $srcDirName => $files
        ]);

        $basePath = ROCKUNIT_RUNTIME;
        $srcDirName = $basePath . DIRECTORY_SEPARATOR . $srcDirName;
        $dstDirName = $basePath . DIRECTORY_SEPARATOR . 'test_dst_dir';

        FileHelper::copyDirectory($srcDirName, $dstDirName);

        $this->assertFileExists($dstDirName, 'Destination directory does not exist!');
        foreach ($files as $name => $content) {
            $fileName = $dstDirName . DIRECTORY_SEPARATOR . $name;
            $this->assertFileExists($fileName);
            $this->assertEquals($content, file_get_contents($fileName), 'Incorrect file content!');
        }
    }

    public function testCopyDirectoryPermissions()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->markTestSkipped("Can't reliably test it on Windows because fileperms() always return 0777.");
        }
        static::clearRuntime();
        $srcDirName = 'test_src_dir';
        $subDirName = 'test_sub_dir';
        $fileName = 'test_file.txt';
        $this->createFileStructure([
            $srcDirName => [
                $subDirName => [],
                $fileName => 'test file content',
            ],
        ]);

        $basePath = ROCKUNIT_RUNTIME;
        $srcDirName = $basePath . DIRECTORY_SEPARATOR . $srcDirName;
        $dstDirName = $basePath . DIRECTORY_SEPARATOR . 'test_dst_dir';

        $dirMode = 0755;
        $fileMode = 0755;
        $options = [
            'dirMode' => $dirMode,
            'fileMode' => $fileMode,
        ];
        FileHelper::copyDirectory($srcDirName, $dstDirName, $options);

        $this->assertFileMode($dirMode, $dstDirName, 'Destination directory has wrong mode!');
        $this->assertFileMode($dirMode, $dstDirName . DIRECTORY_SEPARATOR . $subDirName, 'Copied sub directory has wrong mode!');
        $this->assertFileMode($fileMode, $dstDirName . DIRECTORY_SEPARATOR . $fileName, 'Copied file has wrong mode!');
    }

    public function testRemoveDirectory()
    {
        $dirName = 'test_dir_for_remove';
        $this->createFileStructure([
            $dirName => [
                'file1.txt' => 'file 1 content',
                'file2.txt' => 'file 2 content',
                'test_sub_dir' => [
                    'sub_dir_file_1.txt' => 'sub dir file 1 content',
                    'sub_dir_file_2.txt' => 'sub dir file 2 content',
                ],
            ],
        ]);

        $basePath = ROCKUNIT_RUNTIME;
        $dirName = $basePath . DIRECTORY_SEPARATOR . $dirName;

        FileHelper::deleteDirectory($dirName);

        $this->assertFileNotExists($dirName, 'Unable to remove directory!');

        // should be silent about non-existing directories
        FileHelper::deleteDirectory($basePath . DIRECTORY_SEPARATOR . 'nonExisting');
    }

    public function testRemoveDirectorySymlinks1()
    {
        if (strtolower(substr(PHP_OS, 0, 3)) == 'win') {
            $this->markTestSkipped('Cannot test this on MS Windows since symlinks are uncommon for it.');
        }

        $dirName = 'remove-directory-symlinks-1';
        $this->createFileStructure([
            $dirName => [
                'file' => 'Symlinked file.',
                'directory' => [
                    'standard-file-1' => 'Standard file 1.'
                ],
                'symlinks' => [
                    'standard-file-2' => 'Standard file 2.',
                    'symlinked-file' => ['symlink', '..' . DIRECTORY_SEPARATOR . 'file'],
                    'symlinked-directory' => ['symlink', '..' . DIRECTORY_SEPARATOR . 'directory'],
                ],
            ],
        ]);

        $basePath = ROCKUNIT_RUNTIME . DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR;
        $this->assertFileExists($basePath . 'file');
        $this->assertTrue(is_dir($basePath . 'directory'));
        $this->assertFileExists($basePath . 'directory' . DIRECTORY_SEPARATOR . 'standard-file-1');
        $this->assertTrue(is_dir($basePath . 'symlinks'));
        $this->assertFileExists($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'standard-file-2');
        $this->assertFileExists($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'symlinked-file');
        $this->assertTrue(is_dir($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'symlinked-directory'));
        $this->assertFileExists($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'symlinked-directory' . DIRECTORY_SEPARATOR . 'standard-file-1');

        FileHelper::deleteDirectory($basePath . 'symlinks');

        $this->assertFileExists($basePath . 'file');
        $this->assertTrue(is_dir($basePath . 'directory'));
        $this->assertFileExists($basePath . 'directory' . DIRECTORY_SEPARATOR . 'standard-file-1'); // symlinked directory still have it's file
        $this->assertFalse(is_dir($basePath . 'symlinks'));
        $this->assertFileNotExists($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'standard-file-2');
        $this->assertFileNotExists($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'symlinked-file');
        $this->assertFalse(is_dir($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'symlinked-directory'));
        $this->assertFileNotExists($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'symlinked-directory' . DIRECTORY_SEPARATOR . 'standard-file-1');
    }

    public function testRemoveDirectorySymlinks2()
    {
        if (strtolower(substr(PHP_OS, 0, 3)) == 'win') {
            $this->markTestSkipped('Cannot test this on MS Windows since symlinks are uncommon for it.');
        }

        $dirName = 'remove-directory-symlinks-2';
        $this->createFileStructure([
            $dirName => [
                'file' => 'Symlinked file.',
                'directory' => [
                    'standard-file-1' => 'Standard file 1.'
                ],
                'symlinks' => [
                    'standard-file-2' => 'Standard file 2.',
                    'symlinked-file' => ['symlink', '..' . DIRECTORY_SEPARATOR . 'file'],
                    'symlinked-directory' => ['symlink', '..' . DIRECTORY_SEPARATOR . 'directory'],
                ],
            ],
        ]);

        $basePath = ROCKUNIT_RUNTIME . DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR;
        $this->assertFileExists($basePath . 'file');
        $this->assertTrue(is_dir($basePath . 'directory'));
        $this->assertFileExists($basePath . 'directory' . DIRECTORY_SEPARATOR . 'standard-file-1');
        $this->assertTrue(is_dir($basePath . 'symlinks'));
        $this->assertFileExists($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'standard-file-2');
        $this->assertFileExists($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'symlinked-file');
        $this->assertTrue(is_dir($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'symlinked-directory'));
        $this->assertFileExists($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'symlinked-directory' . DIRECTORY_SEPARATOR . 'standard-file-1');

        FileHelper::deleteDirectory($basePath . 'symlinks', ['traverseSymlinks' => true]);

        $this->assertFileExists($basePath . 'file');
        $this->assertTrue(is_dir($basePath . 'directory'));
        $this->assertFileNotExists($basePath . 'directory' . DIRECTORY_SEPARATOR . 'standard-file-1'); // symlinked directory doesn't have it's file now
        $this->assertFalse(is_dir($basePath . 'symlinks'));
        $this->assertFileNotExists($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'standard-file-2');
        $this->assertFileNotExists($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'symlinked-file');
        $this->assertFalse(is_dir($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'symlinked-directory'));
        $this->assertFileNotExists($basePath . 'symlinks' . DIRECTORY_SEPARATOR . 'symlinked-directory' . DIRECTORY_SEPARATOR . 'standard-file-1');
    }

    /**
     * Creates test files structure,
     * @param array $items file system objects to be created in format: objectName => objectContent
     *                         Arrays specifies directories, other values - files.
     * @param string $basePath structure base file path.
     */
    protected function createFileStructure(array $items, $basePath = '')
    {
        if (empty($basePath)) {
            $basePath = ROCKUNIT_RUNTIME;
        }
        foreach ($items as $name => $content) {
            $itemName = $basePath . DIRECTORY_SEPARATOR . $name;
            if (is_array($content)) {
                if (isset($content[0], $content[1]) && $content[0] == 'symlink') {
                    symlink($content[1], $itemName);
                } else {
                    mkdir($itemName, 0777, true);
                    $this->createFileStructure($content, $itemName);
                }
            } else {
                file_put_contents($itemName, $content);
            }
        }
    }

    /**
     * Asserts that file has specific permission mode.
     * @param integer $expectedMode expected file permission mode.
     * @param string $fileName file name.
     * @param string $message error message
     */
    protected function assertFileMode($expectedMode, $fileName, $message = '')
    {
        $expectedMode = sprintf('%o', $expectedMode);
        $this->assertEquals($expectedMode, $this->getMode($fileName), $message);
    }

    /**
     * Get file permission mode.
     * @param  string $file file name.
     * @return string permission mode.
     */
    protected function getMode($file)
    {
        return substr(sprintf('%o', fileperms($file)), -4);
    }
}