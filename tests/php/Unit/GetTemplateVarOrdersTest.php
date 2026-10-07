<?php

class GetTemplateVarOrdersTest extends ContactformTestCase
{
    public function testGuestProductOnlyThreadDoesNotCreatePhantomOrderBucket()
    {
        $reflection = new ReflectionClass($this->module);
        $property = $reflection->getProperty('customer_thread');
        $property->setAccessible(true);
        $property->setValue($this->module, [
            'id_product' => 5,
            'id_order' => 0,
        ]);

        $orders = $this->module->getTemplateVarOrders();
        $this->assertSame([], $orders);
    }

    public function testProductIsAttachedWhenOrderRowExists()
    {
        OrderRepository::$orders[10] = [
            'id_customer' => 1,
            'products' => [],
        ];
        $reflection = new ReflectionClass($this->module);
        $property = $reflection->getProperty('customer_thread');
        $property->setAccessible(true);
        $property->setValue($this->module, [
            'id_product' => 5,
            'id_order' => 10,
        ]);

        $orders = $this->module->getTemplateVarOrders();
        $this->assertArrayHasKey(10, $orders);
        $this->assertArrayHasKey(5, $orders[10]['products']);
    }
}
