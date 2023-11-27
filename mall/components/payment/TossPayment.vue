<template> 
    <div id="payment-method"></div> 
</template>

<script> 
 
const clientKey = "test_ck_0Poxy1XQL8RePA741xXr7nO5Wmlg";
const paymentWidget = PaymentWidget(clientKey, PaymentWidget.ANONYMOUS);

export default {
  mounted() {
    paymentWidget.renderPaymentMethods("#payment-method", { value: 50000 });
  },
  methods: {
    clickPaymentButton() {
      paymentWidget
        .requestPayment({
          orderId: generateRandomString(),
          orderName: "리팩터링 2판 외 1권",
          successUrl: window.location.origin + "/success",
          failUrl: window.location.origin + "/fail",
          customerEmail: "customer123@gmail.com", // 고객 이메일 (선택)
          customerName: "김토스", // 고객 이름 (선택)
        })
        .then((v) => console.log(v));
    },
  },
};
</script>
