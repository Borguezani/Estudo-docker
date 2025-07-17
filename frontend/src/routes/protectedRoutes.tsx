import { RouteObject } from "react-router-dom";
import Home from "../components/Home";
import ProtectedRoute from "../components/ProtectedRoute";

export default function protectedRoutes(): Array<RouteObject> {
  return [
    { 
      path: '/home', 
      element: (
        <ProtectedRoute>
          <Home />
        </ProtectedRoute>
      ) 
    }
  ];  
}
