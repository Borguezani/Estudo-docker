import { RouteObject } from "react-router-dom";
import Login from "../components/Login";

export default function publicRoutes(): Array<RouteObject> {
  return [
    { path: '/', element: <Login /> },
    { path: '/login', element: <Login /> },
  ];
}
