<?php

$rootDir = dirname(__DIR__);

$psVersionUnderTest = getenv('PS_VERSION_UNDER_TEST');
if (false === $psVersionUnderTest || '' === $psVersionUnderTest) {
    putenv('PS_VERSION_UNDER_TEST=8.1.7');
}

require_once $rootDir . '/vendor/autoload.php';
require_once $rootDir . '/tools/vendor/autoload.php';
require_once __DIR__ . '/stubs/PrestaShopDoubles.php';
require_once $rootDir . '/contactform.php';
require_once __DIR__ . '/ContactformTestCase.php';

if (!class_exists('ShuffledSuiteListener', false)) {
    class ShuffledSuiteListener implements PHPUnit_Framework_TestListener
    {
        private $done = false;

        public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
        {
        }

        public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
        {
        }

        public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
        {
        }

        public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time)
        {
        }

        public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
        {
        }

        public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
        {
            if ($this->done || $suite->getName() !== 'ContactformModuleTests') {
                return;
            }
            $this->done = true;

            $tests = $suite->tests();
            if (count($tests) < 2) {
                return;
            }

            $seed = getenv('TEST_SEED');
            if (false === $seed || '' === $seed) {
                $seed = (string) mt_rand();
            }
            fwrite(STDOUT, "PHPUnit test order seed: {$seed}\n");
            mt_srand((int) $seed);
            shuffle($tests);

            $reflection = new ReflectionClass($suite);
            $property = $reflection->getProperty('tests');
            $property->setAccessible(true);
            $property->setValue($suite, $tests);
        }

        public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
        {
        }

        public function startTest(PHPUnit_Framework_Test $test)
        {
        }

        public function endTest(PHPUnit_Framework_Test $test, $time)
        {
        }
    }
}
