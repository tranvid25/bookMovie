import{
    LAY_PROVINCE,
    LAY_CHITIET_PROVINCE
} from "../constants";
import { history } from "../../App";
import { quanlyProvinceService } from "../../services/QuanLyProvinceService";
export const layDanhSachTinhAction=(id="")=>{
    return async(dispatch)=>{
        try {
          if(id !== "")
          {
            const result=await quanlyProvinceService.layDanhSachTinh(id);
            if(result.status === 200){
                dispatch({
                    type:LAY_CHITIET_PROVINCE,
                    provinceEditDetail:result.data.content,
                });
            }
          }else{
            const result=await quanlyProvinceService.layDanhSachTinh();
            if(result.status === 200){
                dispatch({
                    type:LAY_PROVINCE,
                    arrProvince:result.data.content,
                });
            }
          }
        } catch (error) {
            console.log(error.response?.data);
        }
    }
}
export const themProvinceAction=(FormData)=>{
    return async(dispatch)=>{
        try {
            const result =await quanlyProvinceService.themTinh(FormData);
            alert("Đã thêm Province thành công");
            history.push("/admin/Province");
        } catch (error) {
            console.log(error.response?.data);
        }
    }
}
export const capnhatProvinceAction=(id,formData)=>{
    return async(dispatch)=>{
        try {
            const result=await quanlyProvinceService.capNhatTinh(id,formData);
            alert("Cập nhật Tỉnh thành công");
            history.push("/admin/Province");
        } catch (error) {
            console.log(error.response?.data);
        }
    }
}
export const xoaProvinceAction=(id)=>{
    return async(dispatch)=>{
        try {
            const result=await quanlyProvinceService.XoaTinh(id);
            dispatch(layDanhSachTinhAction());
        } catch (error) {
            console.log("error",error);
        }
    }
}