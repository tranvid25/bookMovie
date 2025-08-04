import React from "react";
import ReactDOM from "react-dom/client";
import { GoogleOAuthProvider } from "@react-oauth/google";
import "./index.css";
import "antd/dist/reset.css";
import App from "./App";
import reportWebVitals from "./reportWebVitals";
import { Provider } from "react-redux";
import { store } from "./redux/configStore";
import "./i18n";
import Loading from "./components/Loading/Loading";

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
  <Provider store={store}>
    <GoogleOAuthProvider 
      clientId={process.env.REACT_APP_GOOGLE_CLIENT_ID || "258197998165-sp0qhea21q8hrqo3jboikklukgr74jpa.apps.googleusercontent.com"}
      onScriptLoadError={() => console.log('Google OAuth script failed to load')}
    >
      <App />
    </GoogleOAuthProvider>
  </Provider>
);
// const preLoader = ReactDOM.render(document.getElementById('preLoader'));
// preLoader.render( <Loading/>
// );

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
reportWebVitals();
