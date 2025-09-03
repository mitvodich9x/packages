<template>
    <div v-if="props.option === 'all'" class="px-6 md:px-4 mt-4">
        <main class="guide">
            <h1 class="text-center">
                Hướng dẫn nạp trên
                <a href="https://nap.vgplay.vn" target="_blank" rel="noopener"
                    >nap.vgplay.vn</a
                >
                (PC)
            </h1>

            <!-- Bước 1 -->
            <h2 class="text-primary font-medium">
                Bước 1: Chọn game tại trang nạp chính thức và duy nhất của VGP
                <a href="https://nap.vgplay.vn" target="_blank" rel="noopener"
                    >https://nap.vgplay.vn</a
                >
            </h2>
            <p>
                Nhấp vào icon của game hoặc tìm theo tên game ở thanh tìm kiếm.
            </p>

            <figure>
                <img
                    src="https://img.vgplay.vn/vgplay/images/1be95e76b3e1b02041109e1266559c24.webp"
                    alt="Màn hình trang nạp chính thức của VGP trên PC"
                />
                <figcaption>
                    <span class="desc text-center block"
                        >Màn hình trang nạp chính thức của VGP
                        <a
                            href="https://nap.vgplay.vn"
                            target="_blank"
                            rel="noopener"
                            >https://nap.vgplay.vn</a
                        >
                        trên PC</span
                    >
                </figcaption>
            </figure>

            <!-- Bước 2 -->
            <h2 class="text-primary font-medium">
                Bước 2: Đăng nhập vào tài khoản game cần mua vật phẩm
            </h2>
            <p>Chọn máy chủ (nếu có) và nhân vật cần mua vật phẩm.</p>

            <figure>
                <img
                    src="https://img.vgplay.vn/vgplay/images/e16bce157c326af0e222cdfd2839c7fb.webp"
                    alt="Màn hình đăng nhập nap.vgplay.vn trên PC"
                />
                <figcaption>
                    <span class="desc text-center block"
                        >Màn hình đăng nhập
                        <a
                            href="https://nap.vgplay.vn"
                            target="_blank"
                            rel="noopener"
                            >https://nap.vgplay.vn</a
                        >
                        trên PC</span
                    >
                </figcaption>
            </figure>

            <!-- Bước 3 -->
            <h2 class="text-primary font-medium">
                Bước 3: Chọn hình thức nạp/vật phẩm cần mua
            </h2>

            <figure>
                <img
                    src="https://img.vgplay.vn/vgplay/images/c12e369ca55812e610bdc840aa215f79.webp"
                    alt="Màn hình chọn gói/vật phẩm trên PC"
                />
                <figcaption>
                    <span class="desc text-center block"
                        >Màn hình chọn gói/vật phẩm của tính năng mua một vật
                        phẩm trên PC</span
                    >
                </figcaption>
            </figure>

            <!-- Bước 4 -->
            <h2 class="text-primary font-medium">
                Bước 4: Kiểm tra thông tin trước khi thanh toán
            </h2>
            <p>
                Kiểm tra lại thông tin một lần tài khoản muốn nạp trước khi ấn
                chọn gói thanh toán.
            </p>
            <p>Nhân vật và máy chủ cần nạp.</p>

            <figure>
                <img
                    src="https://img.vgplay.vn/vgplay/images/c12e369ca55812e610bdc840aa215f79.webp"
                    alt="Màn hình thông tin nhân vật/tài khoản trên PC"
                />
                <figcaption>
                    <span class="desc text-center block"
                        >Màn hình thông tin nhân vật/tài khoản trên PC</span
                    >
                </figcaption>
            </figure>

            <!-- Bước 5 -->
            <h2 class="text-primary font-medium">
                Bước 5: Chọn phương thức thanh toán phù hợp
            </h2>
            <p>Chọn một trong các phương thức thanh toán sau:</p>
            <ul>
                <li>
                    <strong>ATM/Visa Việt Nam</strong>: Chọn hình thức thanh
                    toán và ngân hàng hỗ trợ để thanh toán.
                </li>
                <li>
                    <strong>Visa Quốc Tế</strong>: Nhập thông tin thanh toán.
                </li>
                <li>
                    <strong>QR Ngân hàng</strong>: Thanh toán thông qua quét QR
                    từ ứng dụng ngân hàng/ví điện tử.
                </li>
                <li>
                    <strong>Ứng dụng Momo</strong>: Thanh toán thông qua ví
                    Momo.
                </li>
                <li>
                    <strong>Zalo Pay</strong>: Thanh toán thông qua ví Zalo Pay.
                </li>
                <li>
                    <strong>Google Pay</strong>: Thanh toán thông qua ví Google
                    Pay.
                </li>
                <li>
                    <strong>Samsung Pay</strong>: Thanh toán thông qua ví
                    Samsung Pay.
                </li>
                <li>
                    <strong>Apple Pay</strong>: Thanh toán thông qua ví Apple
                    Pay. <em>Lưu ý:</em> Chỉ xuất hiện tùy chọn này trên các
                    thiết bị hỗ trợ Apple Pay.
                </li>
            </ul>
            <p>
                <strong>Bấm nút “Thanh toán ngay”</strong> để bắt đầu thanh toán
                bằng hình thức đã chọn.
            </p>

            <figure>
                <img
                    src="https://img.vgplay.vn/vgplay/images/7ff381d21cca7338c5a76757832ac9d9.webp"
                    alt="Màn hình chọn phương thức thanh toán trên PC"
                />
                <figcaption>
                    <span class="desc text-center block"
                        >Màn hình chọn phương thức thanh toán trên PC</span
                    >
                </figcaption>
            </figure>

            <!-- Bước 6 -->
            <h2 class="text-primary font-medium">Bước 6: Kết quả giao dịch</h2>
            <p>
                Sau khi giao dịch hoàn tất, gói nạp/vật phẩm mua sẽ nhận được
                trong vòng <strong>1–3 phút</strong>.
            </p>
            <p>
                Có thể kiểm tra giao dịch thành công thông qua màn hình
                <strong>Lịch sử giao dịch</strong>.
            </p>

            <figure>
                <img
                    src="https://img.vgplay.vn/vgplay/images/a1bea5fb8e8e23cc898cda33919ceecd.webp"
                    alt="Cách vào màn hình Lịch sử giao dịch trên PC"
                />
                <figcaption>
                    <span class="desc text-center block"
                        >Cách vào màn hình Lịch sử giao dịch trên PC</span
                    >
                </figcaption>
            </figure>

            <figure>
                <img
                    src="https://img.vgplay.vn/vgplay/images/2e5ddb9f1149391912b7d3e3b76ad480.webp"
                    alt="Màn hình Lịch sử giao dịch trên PC"
                />
                <figcaption>
                    <span class="desc text-center block"
                        >Màn hình Lịch sử giao dịch trên PC</span
                    >
                </figcaption>
            </figure>
        </main>
    </div>
    <div v-else class="px-6 md:px-4 font-[400]">
        <n-card class="mt-4">
            <n-list hoverable clickable>
                <n-list-item class="text-lg">
                    Bước 1: Quý khách click trực tiếp vào Link liên kết (bảng
                    dưới).
                </n-list-item>
            </n-list>
            <n-list hoverable clickable>
                <n-list-item class="text-lg">
                    Bước 2: Chọn mệnh giá và số lượng thẻ VGPCard cần mua vào
                    giỏ hàng.
                </n-list-item>
            </n-list>
            <n-list hoverable clickable>
                <n-list-item class="text-lg"> Bước 3: Tiến hành thanh toán. </n-list-item>
            </n-list>
            <n-list hoverable clickable>
                <n-list-item class="text-lg">
                    Bước 4. Nhận mã thẻ qua SĐT/email đăng ký.
                </n-list-item>
            </n-list>
        </n-card>
        <n-card class="mt-4">
            <n-table :bordered="false" :single-line="false">
                <thead>
                    <tr>
                        <th class="text-center!">STT</th>
                        <th class="text-center!">Tên trang WEB/APP bán thẻ</th>
                        <th class="text-center!">Link liên kết</th>
                    </tr>
                </thead>
                <tbody class="text-lg">
                    <tr>
                        <td class="text-center">1</td>
                        <td class="text-center">Nạp thẻ ngay</td>
                        <td class="text-center">
                            <a
                                href="https://napthengay.vn"
                                target="_blank"
                                class="text-primary"
                                >https://napthengay.vn</a
                            >
                        </td>
                    </tr>
                </tbody>
            </n-table>
        </n-card>
    </div>
</template>

<script setup>
const props = defineProps({
    option: {
        type: String,
        default: "all",
    },
});

</script>

<style scoped>
h1 {
    font-size: 1.75rem;
    margin: 0 0 1rem;
}
h2 {
    font-size: 1.25rem;
    margin: 1.5rem 0 0.5rem;
}
p {
    margin: 0.65rem 0;
}

/* Ảnh + chú thích */
figure {
    margin: 1rem 0;
}
figure img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 12px;
}
figcaption {
    text-align: center;
    font-size: 0.95rem;
    color: #4b5563;
    margin-top: 0.5rem;
}
figcaption .label {
    font-weight: 600;
    display: block;
}
figcaption .desc {
    display: block;
}

/* Danh sách */
ul {
    padding-left: 1.2rem;
    margin: 0.5rem 0;
}
li {
    margin: 0.25rem 0;
}

.font-medium {
    font-weight: 500;
}
</style>
