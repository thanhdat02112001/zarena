<?php
// Tạo các luồng chạy đồng thời
$thread1 = new Thread('bot.php');
$thread2 = new Thread('bot_2.php');

// Bắt đầu chạy các luồng
$thread1->start();
$thread2->start();

// Chờ cho đến khi cả hai luồng hoàn thành
$thread1->join();
$thread2->join();

// Định nghĩa lớp Thread
class Thread extends Thread {
    private $file;

    public function __construct($file) {
        $this->file = $file;
    }

    public function run() {
        exec('php ' . $this->file);
    }
}
?>