import { PayPalButton } from "react-paypal-button-v2";
import { TOKEN } from "../util/settings/config";

export default function PayPalCheckout({ donhang }) {
  // ✅ Lấy accessToken từ localStorage một lần thôi
  const accessToken = localStorage.getItem(TOKEN);

  const handleSuccess = async (details, data) => {
    console.log("Thanh toán thành công:", details, data);
    try {
      const res = await fetch("http://localhost:8000/api/auth/payment/capture-order", {
        method: "POST",
        headers: { 
          "Content-Type": "application/json",
          "Authorization": `Bearer ${accessToken}` // ✅ thêm token nếu BE cần auth
        },
        body: JSON.stringify({ maOrder: donhang.id, orderId: data.orderID }),
      });

      const result = await res.json();
      if (result.success) {
        alert("Thanh toán và capture order thành công!");
      } else {
        alert("Capture order thất bại!");
      }
    } catch (err) {
      console.error(err);
      alert("Lỗi gọi API capture order!");
    }
  };

  return (
    <div>
      <h3>Thanh toán đơn hàng #{donhang.id}</h3>
      <PayPalButton
        onSuccess={handleSuccess}
        createOrder={async () => {
          const res = await fetch("http://localhost:8000/api/auth/payment/create-order", {
            method: "POST",
            headers: { 
              "Content-Type": "application/json",
              "Authorization": `Bearer ${accessToken}` // ✅ thêm token nếu BE cần
            },
            body: JSON.stringify({ maOrder: donhang.id }),
          });

          const { orderId } = await res.json();
          return orderId;
        }}
        options={{
          clientId: "AbBEHhJWcPTzXxGrW4pzPMHIWoizH-fC5URU_mN2HDSLLdVX0r_Aoymvmptj0ZFTlNy-qOqzqyDWvL75",
          currency: "USD",
        }}
      />
    </div>
  );
}
