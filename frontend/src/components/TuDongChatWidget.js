import { useEffect } from 'react';

const TuDongChatWidget = () => {
  useEffect(() => {
    const script = document.createElement('script');
    script.src = 'https://app.tudongchat.com/js/chatbox.js';
    script.async = true;

    script.onload = () => {
      const chatbox = new window.TuDongChat('9uBjSv2IN3XR_IOWX0uac'); // Thay mã ID của bạn ở đây
      chatbox.initial();
    };

    document.body.appendChild(script);

    return () => {
      document.body.removeChild(script);
    };
  }, []);

  return null; // Không render gì
};

export default TuDongChatWidget;
