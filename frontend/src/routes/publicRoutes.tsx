import { RouteObject } from "react-router-dom";
import Login from "../components/Login";
import PublicRoute from "../components/PublicRoute";
import { Navigate } from "react-router-dom";

export default function publicRoutes(): Array<RouteObject> {
  return [
    { 
      path: '/', 
      element: <Navigate to="/login" replace />
    },
    { 
      path: '/login', 
      element: (
        <PublicRoute>
          <Login />
        </PublicRoute>
      ) 
    },
  ];
}
