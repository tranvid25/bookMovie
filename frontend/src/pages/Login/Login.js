import React from "react";
import { Button, Checkbox, Form, Input } from "antd";
import { useDispatch, useSelector } from "react-redux";
import {
  dangNhapAction,
  dangNhapGoogleAction,
} from "../../redux/actions/QuanLyNguoiDungAction";
import { UserReducer } from "./../../redux/reducers/UserReducer";
import { GoogleLogin } from "@react-oauth/google";

export default function Login(props) {
  const dispatch = useDispatch();

  const { userLogin } = useSelector((state) => state.UserReducer);

  const onFinish = (values) => {
    const action = dangNhapAction(values);
    dispatch(action);
  };
  const onFinishFailed = (errorInfo) => {
    console.log("Failed:", errorInfo);
  };

  return (
    <div className="z-20 px-8 py-8 bg-white shadow-xl rounded-2xl">
      <Form
        name="basic"
        className="flex-col d-flex"
        labelCol={{
          span: 8,
        }}
        wrapperCol={{
          span: 24,
        }}
        style={{
          maxWidth: 350,
          width: 350,
          minWidth: "100%",
        }}
        initialValues={{
          remember: false,
        }}
        onFinish={onFinish}
        onFinishFailed={onFinishFailed}
        autoComplete="off"
      >
        <div>
          <h1 className="mb-4 text-3xl font-bold text-center cursor-pointer">
            Đăng Nhập
          </h1>
          <p className="mb-8 text-sm font-semibold tracking-wide text-center text-gray-700">
            Đăng nhập để truy cập vào tài khoản của bạn
          </p>
        </div>
        <Form.Item
          label=""
          name="email"
          style={{ minWidth: "100%" }}
          rules={[
            {
              type: "email",
              message: "E-mail chưa đúng định dạng!",
            },
            {
              required: true,
              message: "E-mail không được để trống!",
              transform: (value) => value.trim(),
            },
          ]}
        >
          <Input
            className="block w-full px-4 py-3 text-sm border rounded-lg outline-none"
            placeholder="Email"
          />
        </Form.Item>

        <Form.Item
          label=""
          name="password"
          rules={[
            {
              required: true,
              message: "Password không được để trống!",
              transform: (value) => value.trim(),
            },
          ]}
        >
          <Input.Password
            className="block w-full px-4 py-3 mt-3 text-sm border rounded-lg outline-none d-flex"
            placeholder="Mật khẩu"
          />
        </Form.Item>

        <Form.Item
          name="remember"
          style={{ textAlign: "left" }}
          valuePropName="checked"
          wrapperCol={{
            offset: 0,
            // span: 16,
          }}
        >
          <div className="justify-between d-flex">
            {/* <Checkbox >Ghi nhớ</Checkbox> */}
            <a
              className="block w-full text-right cursor-pointer"
              href="/forgetPassword"
            >
              Quên mật khẩu
            </a>
          </div>
        </Form.Item>

        <div className="mt-6 text-center">
          <button
            type="submit"
            className="w-64 py-2 text-xl text-white bg-purple-400 rounded-xl"
          >
            Đăng nhập
          </button>

          <p className="mt-4 text-sm">
            Bạn chưa có tài khoản?{" "}
            <a href="register" className="underline cursor-pointer">
              {" "}
              Đăng ký
            </a>
          </p>
        </div>
        <div className="mt-4 text-center">
          <GoogleLogin
            onSuccess={(credentialResponse) => {
              console.log('Google Login Success:', credentialResponse);
              const { credential } = credentialResponse;
              if (credential) {
                dispatch(dangNhapGoogleAction(credential));
              }
            }}
            onError={(error) => {
              console.log("Google Login Failed:", error);
              alert("Đăng nhập Google thất bại! Vui lòng thử lại hoặc kiểm tra cấu hình OAuth.");
            }}
            onScriptLoadError={() => {
              console.log("Google OAuth script failed to load");
              alert("Không thể tải Google OAuth. Vui lòng kiểm tra kết nối internet.");
            }}
            width="300"
            useOneTap={false}
            auto_select={false}
          />
        </div>
      </Form>
    </div>
  );
}
