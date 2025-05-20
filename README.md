Cài đặt:

- Cài git (Git Bash) về máy

- Mở cửa sổ Git Bash rồi gõ `git clone git@github.com:whateveriswhatever/temperature_humdity_sensor_monitor.git`

- Chạy project bằng Apache với PHP (trên hệ điều hành Debian hoặc XAMPP đều được)

- Mở URL `localhost/temperature_humidity_sensor_monitor/index.php` để loát trang chủ

APIs:

- Trong folder `APIs` có 1 file `log_sensor.php` dùng để nhận dữ liệu từ Raspberry Pi Pico

  - Tổ chức dữ liệu dưới dạng JSON gồm 3 miền ("sensor", "temperature", "humidity") VD: {"sensor": "Pico", "temperature": 31.0, "humidity": 92.0}
  - Gửi dữ liệu bằng phương thức POST đến địa chỉ IP của máy chủ, VD: "http://localhost/temperature_humidity_sensor_monitor/APIs/log_sensor.php", dữ liệu gửi đi dưới dạng JSON

- Dữ liệu được gửi đến thành công sẽ được tự động lưu vào file CSV trong tệp data, sau đó sẽ tự động hiển thị trong bảng báo cáo với phân tích ở trang chính
