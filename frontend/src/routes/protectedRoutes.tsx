import { RouteObject } from "react-router-dom";
import Home from "../components/Home";

export default function protectedRoutes(): Array<RouteObject> {
  return [
    { path: '/', element: <Home /> },
    { path: '/home', element: <Home /> }
  ];  
}
