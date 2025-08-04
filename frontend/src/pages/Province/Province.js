import React, { useEffect, userState, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { layDanhSachTinhAction } from "../../redux/actions/QuanLyProvinceAction";
import { Avatar, Card, List, Pagination } from "antd";
import dayjs from "dayjs";

export default function Province() {
  const { arrProvince } = useSelector((state) => state.ProvinceReducer);
  const dispatch = useDispatch();
  useEffect(() => {
    dispatch(layDanhSachTinhAction());
  }, []);
  const reverseArrProvince = arrProvince.slice().reverse();
  const currentPosts = reverseArrProvince;
  const renderProvince = () => {
  return currentPosts.map((item, index) => {
    return (
      <a
        key={index}
        className="block hover:no-underline"
        href={`/province/detail/${item.maTinh}`}
      >
        <div className="relative w-full h-[400px] rounded-xl overflow-hidden shadow-md hover:scale-[1.01] transition-all duration-300">
          <img
            src={item.hinhAnh}
            alt={item.tenTinh}
            className="object-cover w-full h-full"
          />
          <div className="absolute bottom-0 left-0 right-0 p-3 text-white bg-gradient-to-t from-black/70 to-transparent">
            <h3 className="text-lg font-semibold">{item.tenTinh}</h3>
          </div>
        </div>
      </a>
    );
  });
};


  return (
  <div>
    {/* Banner */}
    <div className="relative header__bg-dark header__with-img">
      <div className="absolute inset-0 flex items-center justify-center bg-black/60">
        <h2 className="px-4 text-3xl font-bold text-center text-white md:text-4xl drop-shadow-md">
          Danh Sách Rạp tại từng tỉnh thành
        </h2>
      </div>
    </div>

    {/* Province list */}
    <div className="container px-4 py-10 mx-auto">
      <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
        {renderProvince()}
      </div>
    </div>
  </div>
);

}
