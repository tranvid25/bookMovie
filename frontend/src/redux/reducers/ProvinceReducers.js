import {
    LAY_PROVINCE,
    LAY_CHITIET_PROVINCE
    
} from "../constants";
const initialState={
    arrProvince:[],
    provinceEditDetail:{}
}
export const ProvinceReducer=(state=initialState,action)=>{
    switch(action.type)
    {
        case LAY_PROVINCE:
            state.arrProvince=action.arrProvince;
            return {...state}
        case LAY_CHITIET_PROVINCE:{
            state.provinceEditDetail=action.provinceEditDetail;
            return {...state}
        }
        default:
            return {...state}
    }
}