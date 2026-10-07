<?php

class ModuleShuffledSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new self('ContactformModuleShuffledTests');

        $files = glob(dirname(__DIR__) . '/php/Unit/*Test.php');
        if (false === $files) {
            return $suite;
        }

        $files = array_values(array_filter($files, function ($path) {
            return false === strpos(basename($path), 'UserAgentVersion902');
        }));

        sort($files);

        $seed = getenv('TEST_SEED');
        if (false === $seed || '' === $seed) {
            $seed = (string) mt_rand();
        }

        fwrite(STDOUT, "PHPUnit test order seed: {$seed}\n");
        mt_srand((int) $seed);

        $indices = range(0, count($files) - 1);
        shuffle($indices);

        foreach ($indices as $index) {
            $suite->addTestFile($files[$index]);
        }

        return $suite;
    }
}
