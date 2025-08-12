import { applyMiddleware, createStore, combineReducers } from "redux";
import reduxThunk from 'redux-thunk';
import { CarouselReducer } from './reducers/CarouselReducer';
import { MovieReducer } from './reducers/MovieReducer';
import { RapReducer } from './reducers/RapReducer';
import { UserReducer } from './reducers/UserReducer';
import { QuanLyDatVeReducer } from './reducers/QuanLyDatVeReducer';
import { LoadingReducer } from './reducers/LoadingReducer';
import { NewsReducer } from './reducers/NewsReducer';
import { FeedbackReducer } from './reducers/FeedbackReducer';
import { OrderDetailReducer } from './reducers/OderDetailReducer';
import { ProvinceReducer } from "./reducers/ProvinceReducers";



const rootReducer = combineReducers({
    CarouselReducer,
    MovieReducer,
    RapReducer,
    UserReducer,
    QuanLyDatVeReducer,
    LoadingReducer,
    NewsReducer,
    FeedbackReducer,
    OrderDetailReducer,
    ProvinceReducer
})

export const store = createStore(rootReducer,applyMiddleware(reduxThunk));