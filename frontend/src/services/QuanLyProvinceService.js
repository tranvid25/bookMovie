import { baseService } from "./baseService";
export class QuanLyProvinceService extends baseService{
    // eslint-disable-next-line no-useless-constructor
    constructor() {
        super();
    }
    layDanhSachTinh=(maTinh='')=>{
        if(maTinh !='')
        {
            return this.get(`/api/laydanhsachtinh/${maTinh}`);
        }else
        {
            return this.get(`/api/laydanhsachtinh`);
        }
    }
    themTinh=(formData) =>{
        return this.post(`/api/laydanhsachtinh`,formData);
    }
    layThongTinTinh=(maTinh)=>{
        return this.get(`/api/laydanhsachtinh/${maTinh}`);
    }
    capNhatTinh=(maTinh,formData)=>{
        return this.post(`/api/laydanhsachtinh/${maTinh}/update`,formData);
    }
    XoaTinh=(maTinh)=>{
        return this.delete(`/api/laydanhsachtinh/${maTinh}/delete`);
    }
}
export const quanlyProvinceService=new QuanLyProvinceService();