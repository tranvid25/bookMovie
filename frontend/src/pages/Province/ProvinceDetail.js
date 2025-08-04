import React, { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { useParams } from "react-router-dom";
import { layDanhSachTinhAction } from "../../redux/actions/QuanLyProvinceAction";

export default function ProvinceDetail() {
  const { provinceEditDetail } = useSelector((state) => state.ProvinceReducer);
  const dispatch = useDispatch();
  const { id } = useParams();

  useEffect(() => {
    dispatch(layDanhSachTinhAction(id));
  }, [dispatch, id]);

  // Debug log
  console.log('provinceEditDetail:', provinceEditDetail);
  console.log('id:', id);

  if (!provinceEditDetail || Object.keys(provinceEditDetail).length === 0) {
    return (
      <div className="py-20 text-xl text-center text-gray-500">
        Đang tải chi tiết tỉnh...
      </div>
    );
  }

  return (
    <div>
      {/* Banner với ảnh nền */}
      <div className="relative h-[400px] w-full">
        <img
          src={provinceEditDetail.hinhAnh}
          alt={provinceEditDetail.tenTinh}
          className="object-cover w-full h-full brightness-90"
        />
        <div className="absolute inset-0 flex items-center justify-center bg-black/60">
          <h1 className="text-4xl font-bold text-white drop-shadow-lg">
            {provinceEditDetail.tenTinh}
          </h1>
        </div>
      </div>

      {/* Nội dung chi tiết */}
      <div className="container px-4 py-10 mx-auto">
        <h2 className="mb-4 text-2xl font-semibold">Địa chỉ Google Map</h2>
        <iframe
          src={provinceEditDetail?.googlemap}
          width="600"
          height="450"
          style={{ border: 0 }}
          allowFullScreen=""
          loading="lazy"
          referrerPolicy="no-referrer-when-downgrade"
        />
      </div>
    </div>
  );
}
